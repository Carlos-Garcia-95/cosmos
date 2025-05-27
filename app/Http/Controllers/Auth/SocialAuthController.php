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
    const ID_TIPO_USUARIO_CLIENTE = 3;
    const ID_TIPO_USUARIO_EMPLEADO = 2;
    const ID_TIPO_USUARIO_ADMINISTRADOR = 1;

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Obtener el usuario de Google.
            // Intenta primero sin stateless(). Si obtienes errores de InvalidStateException, prueba con stateless().
            $googleUser = Socialite::driver('google')->user();
            // $googleUser = Socialite::driver('google')->stateless()->user();

            // 1. Intentar encontrar al usuario por su google_id
            $user = User::where('google_id', $googleUser->getId())->first();
            $isNewUserFlow = false; // Bandera para saber si se está creando un nuevo usuario

            if ($user) {
                // CASO 1: Usuario encontrado directamente por google_id
                Auth::login($user, true); // true para "recordarme"
            } else {
                // 2. Si no se encontró por google_id, buscar por email
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    // CASO 2: Usuario encontrado por email, pero google_id era nulo o diferente.
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $user->avatar ?? $googleUser->getAvatar(), // Actualizar avatar si no tiene o el de Google es mejor
                        'email_verified_at' => $user->email_verified_at ?? now(), // Marcar como verificado si no lo estaba
                        // Opcional: Actualizar nombre/apellidos si estaban vacíos y Google los provee
                        'nombre' => $user->nombre ?? $this->splitName($googleUser->getName())['nombre'],
                        'apellidos' => $user->apellidos ?? $this->splitName($googleUser->getName())['apellidos'],
                    ]);
                    Auth::login($user, true);
                    // CASO 3: Usuario no encontrado ni por google_id ni por email. Se crea uno nuevo.
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
                    Auth::login($user, true);
                }
            }

            // --- Lógica de Redirección Post-Login/Creación ---
            // Verificar si el login realmente funcionó antes de proceder con la lógica de perfil
            if (!Auth::check()) {
                return redirect()->route('principal')->withErrors(['auth' => 'Hubo un problema al intentar iniciar tu sesión. Por favor, inténtalo de nuevo.']);
            }

            // Asumimos que $user ahora es el usuario autenticado si Auth::check() es true
            $authenticatedUser = Auth::user();

            // Determinar si el perfil está incompleto
            $profileIsIncomplete = method_exists($authenticatedUser, 'isProfileIncomplete') && $authenticatedUser->isProfileIncomplete();

            if ($isNewUserFlow || $profileIsIncomplete) {
                $message = $isNewUserFlow ?
                    '¡Bienvenido/a! Accede a "Mi Cuenta" para completar tu perfil.' :
                    '¡Bienvenido/a de nuevo! Parece que faltan algunos datos en tu perfil. Accede a "Mi Cuenta" para completarlos.';
                return redirect()->intended(route('principal'))->with('success', $message);
            }

            if ($user) {
                Auth::login($user, true);
                $welcomeMessage = "¡Bienvenido ";
                if ($user->tipo_usuario === self::ID_TIPO_USUARIO_CLIENTE) {
                    $welcomeMessage .= "Cliente {$user->nombre} {$user->apellidos}!";
                } elseif ($user->tipo_usuario === self::ID_TIPO_USUARIO_EMPLEADO) {
                    $welcomeMessage .= "Empleado {$user->nombre} {$user->apellidos}!";
                } elseif ($user->tipo_usuario === self::ID_TIPO_USUARIO_ADMINISTRADOR) {
                    $welcomeMessage .= "Administrador {$user->nombre} {$user->apellidos}!";
                } else {
                    $welcomeMessage .= "{$user->nombre} {$user->apellidos}!";
                }
                return redirect()->intended(route('principal'))->with('success', $welcomeMessage);
            }
        } catch (InvalidStateException $e) {
            return redirect()->route('principal')->withErrors(['auth' => 'Hubo un problema de estado con Google. Intenta de nuevo. Si persiste, borra cookies.']);
        } catch (ClientException $e) {
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'Sin cuerpo de respuesta.';
            return redirect()->route('principal')->withErrors(['auth' => 'Error de comunicación con Google. Verifica tu configuración.']);
        } catch (Exception $e) {
            return redirect()->route('principal')->withErrors(['auth' => 'No se pudo autenticar con Google. Inténtalo de nuevo.']);
        } finally {
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
