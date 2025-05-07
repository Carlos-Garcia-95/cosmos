<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 

class AdminController extends Controller
{
    public function mostrarLogin()
    {
        if (Auth::check()) {
            return redirect()->route('administrador.dashboard');
        }
        return view('administrador.loginAdministrador');
    }

    public function login(Request $request){

        // 1. Validar las credenciales de entrada. Laravel valida que los campos 'email', 'password' y 'codigo_administrador' estén presentes y tengan el formato básico.
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'codigo_administrador' => 'required|string',
        ]);


        // 2. Intentar encontrar un usuario en la base de datos que coincida con el Email Y el Código de Administrador proporcionados.
        $user = Administrator::where('email', $request->email)
                    ->where('codigo_administrador', $request->codigo_administrador)
                    ->first();

        // 3. Si se encontró un usuario con la combinación de email y código...
        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                // 4. Si las tres credenciales (email, código, Y contraseña verificada) son correctas:
                Auth::login($user); // Esto "loguea" al usuario en la sesión.

                $request->session()->regenerate();

                // 5. Redirigir al usuario al dashboard de administración.
                return redirect()->route('administrador.dashboard');

            } else {
                return redirect()->back()->withErrors(['password' => 'Las credenciales proporcionadas no coinciden.'])->withInput($request->only('email', 'codigo_administrador'));
            }
        } else {
            return redirect()->back()->withErrors(['email' => 'Las credenciales proporcionadas no coinciden.'])->withInput($request->only('email', 'codigo_administrador'));
        }
    }

    public function index()
    {
        return view('administrador.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/administrador')->with('success', '¡Sesión de administrador cerrada correctamente!');
    }
}
