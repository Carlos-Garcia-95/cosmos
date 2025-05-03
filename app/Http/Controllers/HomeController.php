<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ciudad;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\PeliculasController;

class HomeController extends Controller
{
    //Función index
    public function index(){
        // Se recuperan las ciudades de la BBDD (HACER CON API¿?)
        $ciudades = Ciudad::all();

        // Se realiza la petición de películas y géneros a la API
        $peliculas = PeliculasController::peticion_peliculas();
        $generos = PeliculasController::peticion_generos();
        
        // Se devuelve la vista principal con los distintos arrays que necesitaremos
        return view('principal', compact('ciudades', 'peliculas', 'generos')); 

    }

}
