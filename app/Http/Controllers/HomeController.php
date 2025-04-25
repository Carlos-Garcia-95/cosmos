<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //Función index
    /* public function index(){
        return view('principal'); //Returneamos la vista llamada principal
    } */
    public function index(Request $request) // Inject Request if not already
    {
        \Log::info('Session data on root page request:', $request->session()->all()); // Log session data on arrival
        // ... rest of your controller logic ...
        return view('principal');
        
    }
}
