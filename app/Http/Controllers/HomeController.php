<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ciudad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\PeliculasController;

class HomeController extends Controller
{
    //Función index
    public function index(){
        // Se recuperan las ciudades de la BBDD (HACER CON API¿?)
        $ciudades = Ciudad::all();
        $menus = DB::table('menus')->get();

        // Se recuperan las películas activas de la BBDD
        $peliculas = PeliculasController::recuperar_peliculas_activas();
        
        // Se devuelve la vista principal con los distintos arrays que necesitaremos
        return view('principal', compact('ciudades', 'peliculas','menus')); 

    }

}
