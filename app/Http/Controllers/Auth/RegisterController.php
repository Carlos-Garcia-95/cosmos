<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
class RegisterController extends Controller
{
    public function registrar(Request $request)
    {
        // Validación de los datos del formulario
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'email_confirmation' => 'required|email|same:email',
            'password' => 'required|string|min:8|confirmed',
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:10',
            'telefono' => 'required|string|max:20',
            'mayor_edad' => 'required|accepted',
            'dni' => 'required|string|max:30|unique:users,dni',
        ]);

        //Si falla...

        if ($validator->fails()) {
            return redirect()->route('registro')
                            ->withErrors($validator)
                            ->withInput();
        }

        // Crear el usuario
        User::create([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'codigo_postal' => $request->codigo_postal,
            'telefono' => $request->telefono,
            'mayor_edad' => $request->mayor_edad === 'on' ? true : false,
            'dni' => $request->dni,
        ]);

        //Redireccionamos a principal con un mensaje
        return redirect()->route('principal')
                        ->with('mensaje', '¡Registro completado con éxito!');
    }
}

