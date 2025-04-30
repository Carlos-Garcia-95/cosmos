<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Ciudad;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class UserController extends Controller
{
    public function datosUser(Request $request): JsonResponse
    {
        $user = Auth::user();

        $userData = [
            'nombre' => $user->nombre,
            'apellidos' => $user->apellidos,
            'email' => $user->email,
            'fecha_nacimiento' => $user->fecha_nacimiento ? Carbon::parse($user->fecha_nacimiento)->format('d/m/Y') : 'No especificada',
            'numero_telefono' => $user->numero_telefono,
            'dni' => $user->dni,
            'direccion' => $user->direccion ?? 'No especificada',
            // Si existe $user->ciudad, va a coger de la tabla ciudad, el nombre relacionado al id elegido. $user->ciudad ? ($user->ciudad->nombre)
            'ciudad' => $user->city->nombre ?? 'No especificada', //Si no tiene ciudad asociada saldrá no especificada, 
            'codigo_postal' => $user->codigo_postal,
            'id_descuento' => $user->id_descuento ?? 'Ninguno',
        ];

        return response()->json($userData);
    }
}
