<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //Función index
    public function index(){
        return view('principal'); //Returneamos la vista llamada principal
    }
}
