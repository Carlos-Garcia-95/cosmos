<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use App\Models\Ciudad;

class HomeController extends Controller
{
    //Función index
    public function index(){
        $ciudades = Ciudad::all();
        
        return view('principal',compact('ciudades')); //Returneamos la vista llamada principal

    }

    //Haz los cambios que veas, solo me daba fallos
    
    /* public function index(Request $request) // Inject Request if not already
    {
        $ciudades = Ciudad::all();
        \Log::info('Session data on root page request:', $request->session()->all()); // Log session data on arrival
        // ... rest of your controller logic ...
        return view('principal',compact('ciudades'));
        
    } */
}
