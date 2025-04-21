<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

//Ruta por get, al poner / en el buscador, nos saldra la pantalla de principal, que es devuelta por la clase HomeController y llama a la función index.
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('principal');

//Registro
Route::post('/register', [RegisterController::class, 'registrar'])->name('registro');

//Login
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/login', [LoginController::class, 'login'])->name('login');

