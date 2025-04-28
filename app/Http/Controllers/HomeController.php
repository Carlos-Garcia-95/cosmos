<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ciudad;

class HomeController extends Controller
{
    //Función index
    public function index(){
        $ciudades = Ciudad::all();
        
        return view('principal', ['ciudades' => $ciudades]); //Returneamos la vista llamada principal

    }

}
