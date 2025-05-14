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
use App\Http\Controllers\Auth\SalaController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Auth\FechaController;
use App\Http\Controllers\RecuperarAsientos;
use App\Http\Controllers\RecuperarSesionPelicula;

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
    ->middleware('auth:admin');

//Login
Route::post('/administrador', [AdminController::class, 'login'])->name('admin.login.submit');

//Logout
Route::post('/administrador/logout', [AdminController::class, 'logout'])->name('administrador.logout');

//Ruta para buscar películas en la API
Route::get('/administrador/buscar-peliculas-api', [AdminController::class, 'searchTMDb'])->name('admin.searchTMDb');

//Ruta para añadir pelicula
Route::post('/administrador/movies', [AdminController::class, 'storeMovie'])->name('admin.storeMovie');

//Ruta para obtener las peliculas de la base de datos
Route::get('/administrador/manage-movies', [AdminController::class, 'obtenerPeliculas'])->name('obtenerPeliculas');

// Ruta para cambiar el estado 'activa' de una película específica por su ID
Route::patch('administrador/movies/{id}/estadoActivo', [AdminController::class, 'estadoPelicula'])->name('estadoPelicula');

//Ruta para cambiar de estreno a cartelera
Route::patch('administrador/movies/{id}/estrenoActivo', [AdminController::class, 'EstrenoStatus'])->name('EstrenoEstado');

//Ruta para obtener los menus de la base de datos
Route::get('administrador/menu', [AdminController::class, 'obtenerMenu'])->name('obtenerMenu');

//Ruta para cambiar el estado a activo o desactivado
Route::patch('administrador/menu/{id}/estadoActivo', [AdminController::class, 'estadoActivo'])->name('estadoActivo');

//Ruta para añadir nuevo elemento a la base de datos
Route::post('administrador/menu', [AdminController::class, 'añadirProducto'])->name('añadirProducto');

//Ruta para obtener detalles de cada producto para poderlos editar
Route::get('administrador/menu/{id}', [AdminController::class, 'obtenerProducto'])->name('obtenerProducto');

//Ruta para actualizar los detalles de cada producto
Route::put('administrador/menu/{id}', [AdminController::class, 'actualizarProducto'])->name('actualizarProducto');

//Ruta para activar peliculas en cartelera
Route::get('administrador/peliculas/activas-en-cartelera', [SessionController::class, 'getPeliculasActivasEnCartelera']);

// Ruta para obtener la lista de salas (para el select, aunque por ahora solo sea 1)
Route::get('administrador/salas', [SalaController::class, 'getSalas'])->name('admin.getSalas');

// Ruta para obtener las horas disponibles para una fecha, película y sala (para el select dinámico)
Route::get('administrador/sesiones/horas-disponibles', [SessionController::class, 'getHorasDisponibles'])->name('admin.getHorasDisponibles');

// Ruta para crear una nueva sesión
Route::post('administrador/sesiones', [SessionController::class, 'storeSesion'])->name('admin.storeSesion');

// Ruta para obtener las fechas disponibles
Route::get('administrador/fechas/disponibles', [FechaController::class, 'getFechasDisponibles'])->name('admin.getFechasDisponibles');

//Ruta para obtener sesione spor fecha
Route::get('administrador/sesiones-por-fecha/{fecha_id}', [SessionController::class, 'getSessionsByDate']);

//Ruta para eliminar sesiones
Route::delete('administrador/sesiones/{sesion_id}', [SessionController::class, 'deleteSession']);

// Recuperar las sesiones asociadas con una película
Route::get('/recuperar_sesiones/id_pelicula={peliculaId}', [RecuperarSesionPelicula::class, 'recuperar_sesion_pelicula']);

// Recuperar los asientos de la sesión seleccionada
Route::get('/recuperar_asientos/id_sesion={id_sesion}', [RecuperarAsientos::class, 'recuperar_asientos_sesion']);

// Ruta para procesar el envío del formulario
Route::post('administrador/users', [AdminController::class, 'crearEmpleado'])->name('users.store');

// Ruta para comprobar si el email existe
Route::get('administrador/check-email', [CheckController::class, 'checkEmail']);

// Ruta para comprobar si el DNI existe
Route::get('administrador/check-dni', [CheckController::class, 'checkDni']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


