<?php

use Illuminate\Support\Facades\Route;

//Ruta por get, al poner / en el buscador, nos saldra la pantalla de principal, que es devuelta por la clase HomeController y llama a la función index.
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('principal');

