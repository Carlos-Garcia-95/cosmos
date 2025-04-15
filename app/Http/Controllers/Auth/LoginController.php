<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request) {
        $credentials = $request->validate([
            'login_email' => ['required', 'email'],
            'login_password' => ['required']
        ]);

        if (Auth::attempt(['email' => $credentials['login_email'], 'password' => $credentials['login_password']])) {
            $request->session()->regenerate();
            return redirect()->intended('index');
        }

        return back()->withErrors([
            'login_email' => 'El email introducido no es correcto'
        ]);

        // TODO -> Comprobar que el login es correcto
        // TODO -> Si es correcto, averiguar como mostrar el login correcto al usuario
        //         (quizás guardar en sesión)
    }
}
