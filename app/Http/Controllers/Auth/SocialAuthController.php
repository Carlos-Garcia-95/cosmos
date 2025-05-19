<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite; // Descomentar después de Composer
use App\Models\User; // Necesitamos el modelo User
use Illuminate\Support\Facades\Auth; // Para loguear
use Illuminate\Support\Facades\Log; // Para errores


class SocialAuthController extends Controller
{
    // La función redirectToGoogle() se queda igual
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Maneja la respuesta de callback de Google. SOLO para LOGUEAR usuarios existentes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // 1. Obtener los datos del usuario de Google
            $googleUser = Socialite::driver('google')->user();

            // 2. Buscar al usuario en nuestra base de datos por su email
            $user = User::where('email', $googleUser->getEmail())->first();

            // 3. Si el usuario SÍ existe, procedemos a loguearlo
            if ($user) {
                // Opcional: Si el usuario existe pero no tenía asociado su google_id, se lo añadimos
                if (is_null($user->google_id)) {
                    $user->google_id = $googleUser->getId();
                    $user->save();
                    Log::info('Usuario existente vinculado a Google ID: ' . $user->id);
                }

                // 4. Loguear al usuario existente
                Auth::guard('web')->login($user, true); // Usar tu guard de usuario regular

                // 5. Redirigir al usuario a donde necesites después del login
                return redirect()->intended('/'); // O la ruta que corresponda

            } else {
                // Si el usuario NO existe en nuestra DB con ese email de Google,
                // NO podemos crearlo porque faltarían datos obligatorios (DNI, fecha_nacimiento, etc.).
                // Redirigimos de vuelta al login con un mensaje de error.
                Log::warning('Intento de login con Google para email no existente: ' . $googleUser->getEmail());
                return redirect('/login')->with('error', 'Tu cuenta de Google no está asociada a un usuario existente. Por favor, regístrate primero usando el formulario habitual.');
            }

        } catch (\Exception $e) {
            // Manejar otros errores (conexión, configuración, etc.)
            Log::error('Error en el callback de Google durante el intento de login: ' . $e->getMessage(), ['exception' => $e]);
            return redirect('/login')->with('error', 'No se pudo iniciar sesión con Google. Inténtalo de nuevo.');
        }
    }
}