<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    public function mostrar_ciudades() {
        $ciudades = Ciudad::all();

        return view('principal', ['ciudades' => $ciudades]);
    }
}
