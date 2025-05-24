<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Str; // Puedes quitarlo si no generas passwords aleatorios
use Exception;
use Laravel\Socialite\Two\InvalidStateException;
use GuzzleHttp\Exception\ClientException;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        Log::info('--- [GOOGLE_CALLBACK] Iniciando ---');
        try {
            // Obtener el usuario de Google.
            // Intenta primero sin stateless(). Si obtienes errores de InvalidStateException, prueba con stateless().
            $googleUser = Socialite::driver('google')->user();
            // $googleUser = Socialite::driver('google')->stateless()->user();

            Log::info('[GOOGLE_CALLBACK] Datos de Google recibidos:', [
                'google_user_id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar()
            ]);

            // 1. Intentar encontrar al usuario por su google_id
            $user = User::where('google_id', $googleUser->getId())->first();
            $isNewUserFlow = false; // Bandera para saber si se está creando un nuevo usuario

            if ($user) {
                // CASO 1: Usuario encontrado directamente por google_id
                Log::info('[GOOGLE_CALLBACK] Usuario encontrado por google_id.', [
                    'user_id_db' => $user->id,
                    'email_db' => $user->email
                ]);
                Auth::login($user, true); // true para "recordarme"
                Log::info('[GOOGLE_CALLBACK] Login vía google_id. Auth::check(): ' . (Auth::check() ? 'EXITOSO' : 'FALLIDO'));
            } else {
                Log::info('[GOOGLE_CALLBACK] Usuario NO encontrado por google_id. Buscando por email: ' . $googleUser->getEmail());
                // 2. Si no se encontró por google_id, buscar por email
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    // CASO 2: Usuario encontrado por email, pero google_id era nulo o diferente.
                    // Se procede a vincular (o actualizar) el google_id.
                    Log::info('[GOOGLE_CALLBACK] Usuario encontrado por email (google_id no coincidía o era nulo).', [
                        'user_id_db' => $user->id,
                        'email_db' => $user->email,
                        'google_id_actual_db' => $user->google_id
                    ]);
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $user->avatar ?? $googleUser->getAvatar(), // Actualizar avatar si no tiene o el de Google es mejor
                        'email_verified_at' => $user->email_verified_at ?? now(), // Marcar como verificado si no lo estaba
                        // Opcional: Actualizar nombre/apellidos si estaban vacíos y Google los provee
                        'nombre' => $user->nombre ?? $this->splitName($googleUser->getName())['nombre'],
                        'apellidos' => $user->apellidos ?? $this->splitName($googleUser->getName())['apellidos'],
                    ]);
                    Log::info('[GOOGLE_CALLBACK] Usuario actualizado con nuevo google_id y/o avatar.');
                    Auth::login($user, true);
                    Log::info('[GOOGLE_CALLBACK] Login vía email y vinculación de google_id. Auth::check(): ' . (Auth::check() ? 'EXITOSO' : 'FALLIDO'));
                } else {
                    // CASO 3: Usuario no encontrado ni por google_id ni por email. Se crea uno nuevo.
                    Log::info('[GOOGLE_CALLBACK] Usuario NO encontrado por email. Creando nuevo usuario.');
                    $isNewUserFlow = true; // Marcar que estamos en el flujo de creación de nuevo usuario
                    $nameParts = $this->splitName($googleUser->getName());

                    $user = User::create([
                        'nombre' => $nameParts['nombre'],
                        'apellidos' => $nameParts['apellidos'],
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'password' => null, // No se necesita contraseña para login social
                        'email_verified_at' => now(), // Asumimos que Google verifica el email
                        // Los demás campos serán NULL por defecto si son nullable en la BD
                    ]);
                    Log::info('[GOOGLE_CALLBACK] Nuevo usuario creado.', [
                        'user_id_db' => $user->id,
                        'email_db' => $user->email
                    ]);
                    Auth::login($user, true);
                    Log::info('[GOOGLE_CALLBACK] Login de nuevo usuario. Auth::check(): ' . (Auth::check() ? 'EXITOSO' : 'FALLIDO'));
                }
            }

            // --- Lógica de Redirección Post-Login/Creación ---
            // Verificar si el login realmente funcionó antes de proceder con la lógica de perfil
            if (!Auth::check()) {
                Log::error('[GOOGLE_CALLBACK] Auth::login() fue llamado, pero Auth::check() es false. El login no persistió.', ['user_id_intentado' => $user->id ?? 'N/A']);
                return redirect()->route('principal')->withErrors(['google_error' => 'Hubo un problema al intentar iniciar tu sesión. Por favor, inténtalo de nuevo.']);
            }

            // Asumimos que $user ahora es el usuario autenticado si Auth::check() es true
            $authenticatedUser = Auth::user();

            // Determinar si el perfil está incompleto
            $profileIsIncomplete = method_exists($authenticatedUser, 'isProfileIncomplete') && $authenticatedUser->isProfileIncomplete();
            Log::info('[GOOGLE_CALLBACK] Estado del perfil:', ['isNewUserFlow' => $isNewUserFlow, 'profileIsIncomplete' => $profileIsIncomplete]);

            if ($isNewUserFlow || $profileIsIncomplete) {
                $message = $isNewUserFlow ?
                    '¡Bienvenido/a! Accede a "Mi Cuenta" para completar tu perfil.' :
                    '¡Bienvenido/a de nuevo! Parece que faltan algunos datos en tu perfil. Accede a "Mi Cuenta" para completarlos.';
                Log::info('[GOOGLE_CALLBACK] Redirigiendo a principal con mensaje: ' . $message);
                return redirect()->intended(route('principal'))->with('status', $message);
            }

            Log::info('[GOOGLE_CALLBACK] Redirigiendo a principal (usuario existente, perfil completo).');
            return redirect()->intended(route('principal'));
        } catch (InvalidStateException $e) {
            Log::error('[GOOGLE_CALLBACK] InvalidStateException: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('principal')->withErrors(['google_error' => 'Hubo un problema de estado con Google. Intenta de nuevo. Si persiste, borra cookies.']);
        } catch (ClientException $e) {
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'Sin cuerpo de respuesta.';
            Log::error('[GOOGLE_CALLBACK] ClientException: ' . $errorBody, ['trace' => $e->getTraceAsString()]);
            return redirect()->route('principal')->withErrors(['google_error' => 'Error de comunicación con Google. Verifica tu configuración.']);
        } catch (Exception $e) {
            Log::error('[GOOGLE_CALLBACK] Exception general: ' . $e->getMessage(), [
                'exception_type' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('principal')->withErrors(['google_error' => 'No se pudo autenticar con Google. Inténtalo de nuevo.']);
        } finally {
            Log::info('--- [GOOGLE_CALLBACK] Finalizando ---');
        }
    }

    protected function splitName($fullName)
    {
        if (empty($fullName)) {
            return ['nombre' => null, 'apellidos' => null];
        }
        $parts = explode(' ', trim($fullName), 2);
        return [
            'nombre' => $parts[0],
            'apellidos' => $parts[1] ?? null,
        ];
    }

    // Este método es un helper DENTRO de este controlador.
    // Es MEJOR tener una lógica similar en el MODELO User (`$user->isProfileIncomplete()`)
    // para mantener el controlador más limpio y la lógica de negocio en el modelo.
    protected function isProfileIncomplete(User $user): bool
    {
        if (method_exists($user, 'checkIfProfileIsActuallyIncomplete')) { // Usar un nombre diferente si lo pones en el modelo
            return $user->checkIfProfileIsActuallyIncomplete();
        }
        // Lógica de respaldo o la que estés usando:
        return is_null($user->nombre) || // Podrías quitar nombre/apellidos si siempre vienen de Google
            is_null($user->apellidos) ||
            is_null($user->fecha_nacimiento) ||
            is_null($user->numero_telefono) ||
            is_null($user->dni) ||
            is_null($user->ciudad_id) ||
            is_null($user->codigo_postal) ||
            !$user->mayor_edad_confirmado;
    }
}
