<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\CheckController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\MenuController;
use App\Http\Controllers\CiudadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PeliculasController;

//Ruta por get, al poner / en el buscador, nos saldra la pantalla de principal, que es devuelta por la clase HomeController y llama a la función index.
Route::get('/', [HomeController::class, 'index'])->name('principal');

//Registro
Route::post('/register', [RegisterController::class, 'registrar'])->name('registro');

// Ruta para comprobar si el email existe
Route::get('/check-email', [CheckController::class, 'checkEmail']);

// Ruta para comprobar si el DNI existe
Route::get('/check-dni', [CheckController::class, 'checkDni']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

//Ruta para los datos del usuario
Route::get('/perfil/datos', [UserController::class, 'datosUser'])->name('user.datosUser')->middleware('auth');

//Ruta para modificar los datos del usuario en el modal de Mi Cuenta
Route::patch('/perfil/modificar', [UserController::class, 'modificarUser'])->name('name.modificarUser')->middleware('auth');

//Ruta para devolver las ciudades
Route::get('/ciudades', [CiudadController::class, 'pasar_ciudades'])->name('ciudades.pasar_ciudades');

//Ruta para ir al login de administradores
Route::get('/administrador',[AdminController::class, 'mostrarLogin'])->name('administrador.loginAdministrador');

//Ruta para el dashboard
Route::get('/administrador/dashboard', [AdminController::class, 'index'])
    ->name('administrador.dashboard')
    ->middleware('auth');

//Login
Route::post('/administrador', [AdminController::class, 'login'])->name('admin.login.submit');

//Logout
Route::post('/administrador/logout', [AdminController::class, 'logout'])->name('administrador.logout');

//Ruta para buscar películas en la API
Route::get('/administrador/buscar-peliculas-api', [AdminController::class, 'searchTMDb'])->name('admin.searchTMDb');

//Ruta para añadir pelicula
Route::post('/administrador/movies', [AdminController::class, 'storeMovie'])->name('admin.storeMovie');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

