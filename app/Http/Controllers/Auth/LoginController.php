<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log as FacadesLog;

class LoginController extends Controller
{
    public function login(Request $request) {
        $credentials = $request->validate([
            'login_email' => ['required', 'email'],
            'login_password' => ['required']
        ]);

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
            \Log::info('Session data after Auth::attempt success:', $request->session()->all()); // Log the session data
            return redirect()->intended('/')->with('success', 'Login successful!');
            
        }

        return back()->withErrors([
            'login_email' => 'El email o la contraseña no son correctos'
        ])->withInput();

        // TODO -> Comprobar que el login es correcto
        // TODO -> Si es correcto, averiguar como mostrar el login correcto al usuario
        //         (quizás guardar en sesión)
    }

    public function showLoginForm()
    {
        return view('components.login'); // Assuming your login form view is at resources/views/auth/login.blade.php
    }

}
