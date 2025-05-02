<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModificarUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class UserController extends Controller
{
    public function datosUser(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user) {
            $user->load('city');
        }

        $userData = [
            'nombre' => $user->nombre,
            'apellidos' => $user->apellidos,
            'email' => $user->email,
            'fecha_nacimiento' => $user->fecha_nacimiento ? Carbon::parse($user->fecha_nacimiento)->format('d/m/Y') : 'No especificada',
            'numero_telefono' => $user->numero_telefono,
            'dni' => $user->dni,
            'direccion' => $user->direccion ?? 'No especificada',
            // Si existe $user->city, va a coger de la tabla ciudad, el nombre relacionado al id elegido. $user->city ? ($user->city->nombre)
            'ciudad' => $user->city->nombre ?? 'No especificada', //Si no tiene ciudad asociada saldrá no especificada, 
            'codigo_postal' => $user->codigo_postal,
            'id_descuento' => $user->id_descuento ?? 'Ninguno',
        ];

        return response()->json($userData);
    }

    public function modificarUser(ModificarUserRequest $request): JsonResponse {

        $validarDatos = $request->validated(); //Validar los datos

        $user = Auth::user(); //Obtenemos el usuario

        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado.'], 401);
        }

        $user->nombre = ucfirst($validarDatos['nombre']); //Primera con mayusculas
        $user->apellidos = ucfirst($validarDatos['apellidos']); //Primera con mayusculas
        $user->numero_telefono = $validarDatos['numero_telefono'];
        $user->direccion = $validarDatos['direccion'];
        $user->ciudad = $validarDatos['ciudad_id'];
        $user->codigo_postal = $validarDatos['codigo_postal'];

        $user->save();

        $user->load('city'); //Si se cambio la ciudad recargamos el cambio

        $usuarioModificado = [
            'nombre' => $user->nombre,
            'apellidos' => $user->apellidos,
            'email' => $user->email,
            'fecha_nacimiento' => $user->fecha_nacimiento ? Carbon::parse($user->fecha_nacimiento)->format('d/m/Y') : 'No especificada',
            'numero_telefono' => $user->numero_telefono,
            'dni' => $user->dni,
            'direccion' => $user->direccion ?? 'No especificada',
            'ciudad' => $user->city ? ($user->city->nombre ?? 'No especificada') : 'No especificada',
            'ciudad_id' => $user->ciudad_id,
            'codigo_postal' => $user->codigo_postal,
            'id_descuento' => $user->id_descuento ?? 'Ninguno',
        ];

        return response()->json([
            'message' => 'Perfil actualizado corectamente.',
            'user' => $usuarioModificado
        ]);

        

    }
}
