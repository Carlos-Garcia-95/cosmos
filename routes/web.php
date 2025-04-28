<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\CheckController;
use App\Http\Controllers\CiudadController;

//Ruta por get, al poner / en el buscador, nos saldra la pantalla de principal, que es devuelta por la clase HomeController y llama a la función index.
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('principal');

//Registro
Route::post('/register', [RegisterController::class, 'registrar'])->name('registro');

// Ruta para comprobar si el email existe
Route::get('/check-email', [CheckController::class, 'checkEmail']);

// Ruta para comprobar si el DNI existe
Route::get('/check-dni', [CheckController::class, 'checkDni']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

//Login -> Pasa por middleware (para controlar sesión)
// PROBAR SI ESTO VALE DE ALGO
/* Route::middleware(['web'])->group(function () {
    Route::get('HomeController', function () {
        return view('index');
    })->name('index');
}); */

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


// Controlador Tabla Ciudades
//Route::get('/', [CiudadController::class, 'mostrar_ciudades'])->name('ciudades_dropdown');


