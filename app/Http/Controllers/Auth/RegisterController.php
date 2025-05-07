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
            'email' => 'required|string|email|unique:users,email,regex:/^[^<>]*$/',
            'email_confirmation' => 'required|string|email|same:email',
            'password' => 'required|string|min:8|confirmed',
            'nombre' => 'required|string|max:255|alpha',
            'apellidos' => 'required|string|max:255|alpha',
            'direccion' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\/\-#.,]*$/',
            'ciudad' => 'required',
            'codigo_postal' => 'required|string|max:10',
            'telefono' => 'required|digits:9',
            'dni' => 'required|regex:/^\d{8}[A-Za-z]$/',
            'mayor_edad' => 'required|accepted',
            'fecha_nacimiento' => 'required|date',
        ]);

        //Si falla la validación...
        if ($validator->fails()) {
            return redirect()->route('principal')
            ->withErrors($validator, 'registro')
            ->withInput();
        }

        // Crear el usuario
        User::create([
            'nombre' => $request->nombre,                     
            'apellidos' => $request->apellidos,               
            'email' => $request->email,                       
            'fecha_nacimiento' => $request->fecha_nacimiento, 
            'numero_telefono' => $request->telefono,          
            'dni' => $request->dni,                           
            'direccion' => $request->direccion,               
            'ciudad' => $request->ciudad,                     
            'codigo_postal' => $request->codigo_postal,       
            'password' => Hash::make($request->password),    
            'mayor_edad' => $request->mayor_edad === 'on' ? true : false,      
        ]);
        

        //Redireccionamos a principal con un mensaje
        return redirect()->route('principal')
            ->with('success', '¡Registro completado con éxito!');
    }

    

}
