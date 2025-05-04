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
    public function login(Request $request) {

        $mensajes = [
            'login_email.required' => 'Por favor, introduce tu dirección de email.',
            'login_email.email' => 'Por favor, introduce una dirección de email válida.',
            'login_password.required' => 'Por favor, introduce tu contraseña.',
        ];

        // Comprueba que los campos se han rellenado correctamente
        $credentials = $request->validate([
            'login_email' => ['required', 'email'],
            'login_password' => ['required']
        ], $mensajes);

        // Comprobar email correcto
        $user = User::where('email', $credentials['login_email'])->first();

        if (!$user) {
            return back()->withErrors([
                'login_email' => 'La contraseña o el email introducido no es correcto.'
            ])->withInput();
        }

        // Comprobar password correcta
        if (!Hash::check($credentials['login_password'], $user->password)) {
            return back()->withErrors([
                'login_password' => 'La contraseña o el email introducido no es correcto.'
            ])->withInput();
        }

        // Intentar login. Si es exitoso, vuelve a index con las sesión iniciada
        if (Auth::attempt(['email' => $credentials['login_email'], 'password' => $credentials['login_password']], $request->has('remember'))) {
            $request->session()->regenerate();
            Log::info('Session data after Auth::attempt success:', $request->session()->all()); 
            return redirect()->intended('/')->with('success', '¡Bienvenido!');
            
        }

        return back()->withErrors([
            'login_email' => 'El email o la contraseña no son correctos'
        ])->withInput();

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
