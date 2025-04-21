<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Ciudad;

class RegisterController extends Controller
{

    public function mostrarCiudades()
    {
        // Obtener todas las ciudades desde la base de datos
        $ciudades = [
            'Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Zaragoza',
            'Málaga', 'Murcia', 'Palma de Mallorca', 'Las Palmas de Gran Canaria', 'Lleida',
            'Bilbao', 'Alicante', 'Córdoba', 'Valladolid', 'Cáceres',
            'Salamanca', 'Girona', 'Toledo', 'Badajoz', 'Oviedo'
        ]; 

        // Pasar las ciudades a la vista
        return view('principal', ['ciudades' => $ciudades]);

    }
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
            'ciudad' => 'required',
            'codigo_postal' => 'required|string|max:10',
            'telefono' => 'required|digits:9',
            'dni' => 'required|regex:/^\d{8}[A-Za-z]$/',
            'mayor_edad' => 'required|accepted',
            'fecha_nacimiento' => 'required|date',
        ]);

        //Si falla la validación...
        if ($validator->fails()) {
            dd($validator->errors()->toArray());
            return redirect()->route('registro')
                ->withErrors($validator)
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
            ->with('mensaje', '¡Registro completado con éxito!');
    }

    

}
