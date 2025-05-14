<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    const ID_TIPO_USUARIO_CLIENTE = 3;
    const ID_TIPO_USUARIO_EMPLEADO = 2;
    const ID_TIPO_USUARIO_ADMINISTRADOR = 1;

    public function login(Request $request) {

        $mensajes = [
            'login_email.required' => 'Por favor, introduce tu dirección de email.',
            'login_email.email' => 'Por favor, introduce una dirección de email válida.',
            'login_password.required' => 'Por favor, introduce tu contraseña.',
        ];

        // Validar los campos de email y contraseña del formulario
        // Los nombres de los campos en el formulario deben ser 'login_email' y 'login_password'
        $credentials = $request->validate([
            'login_email' => ['required', 'email'],
            'login_password' => ['required']
        ], $mensajes);

        // Preparamos las credenciales para Auth::attempt.
        // Auth::attempt espera las claves 'email' y 'password' por defecto.
        $authCredentials = [
            'email' => $credentials['login_email'],
            'password' => $credentials['login_password'],
        ];

        // --- Intentar login estándar usando email y password ---
        if (Auth::attempt($authCredentials, $request->has('remember'))) {

            // --- PASO 1: Login estándar exitoso en tabla 'users' ---
            $user = Auth::user();

            // --- PASO 2: Verificar si el tipo_usuario es 'Cliente'/'Empleado'/'Administrador' ---
            if ($user->tipo_usuario === self::ID_TIPO_USUARIO_CLIENTE) {

                $request->session()->regenerate();
                Log::info('Session data after Auth::attempt success:', $request->session()->all());

                return redirect()->intended('/')->with('success', "¡Bienvenido Cliente {$user->nombre} {$user->apellidos}!");

            } else if($user->tipo_usuario === self::ID_TIPO_USUARIO_EMPLEADO){

                $request->session()->regenerate();
                Log::info('Session data after Auth::attempt success:', $request->session()->all());

                return redirect()->intended('/')->with('success', "¡Bienvenido Empleado {$user->nombre} {$user->apellidos}!");
            } else if($user->tipo_usuario === self::ID_TIPO_USUARIO_ADMINISTRADOR){

                $request->session()->regenerate();
                Log::info('Session data after Auth::attempt success:', $request->session()->all());

                return redirect()->intended('/')->with('success', "¡Bienvenido Administrador {$user->nombre} {$user->apellidos}!");
            }

        } else {
            // --- PASO 5: El login falló. ---
            return back()->withErrors([
                'login_email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.' // Mensaje genérico de error de credenciales
            ])->onlyInput('login_email'); // Mantener el email en el formulario
        }
    }

    public function showLoginForm()
    {
        return view('components.login');
    }

     //Método para cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();

         // Invalida la sesión actual y regenera el token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', '¡Sesión cerrada correctamente!');
    }

}
