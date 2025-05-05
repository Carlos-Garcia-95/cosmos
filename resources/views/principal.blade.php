<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cosmos Cinema</title>

    <!--GSAPLaravel_8 No borrar, para entrar en GSAP. correo diegito866@gmail.com-->
    @vite([
        'resources/css/app.css', 
        'resources/js/app.js', 
        'resources/css/slider.css', 
        'resources/js/registro.js', 
        'resources/js/login.js', 
        'resources/js/entradas.js', 
        'resources/css/compraEntradas.css', 
        'resources/js/compraEntradas.js',
        'resources/js/user.js',
        'resources/css/user_modal.css', 
    ])
    <!-- Revisar Manera de introducir js y css en blade con vite(npm)-->

</head>

<body id="general">
    <div id='top'></div>
    <section class="cloneable">
        
        <div class="main">
            <section class="header-section">
                <div class="header-buttons" style="display: flex; align-items: center;">
                    @if (session('success'))
                    <div id="flash-message" class="success-message">
                        {{ session('success') }}
                    </div>
                    @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const flashMessage = document.getElementById('flash-message');
                            if (flashMessage && flashMessage.textContent.trim() !== '') {
                                flashMessage.classList.add('show');
                                setTimeout(function() {  
                                    flashMessage.classList.remove('show');
                                    setTimeout(() => {
                                    flashMessage.textContent = '';
                                    }, 500);
                                }, 3000);
                            }
                        });
                    </script>
                    @endpush
                    @endif
                    <button id="mostrarMenus"> <a href="#seccionMenu">CARTA COSMOS</a></button>
                    <button id="mostrarCompra"> <a href="#seccionCompra">COMPRAR ENTRADAS</a></button>
                    @if(Auth::check())
                    <button id="miCuenta">MI CUENTA</button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" id="logout">
                            LOGOUT
                        </button>
                    </form>
                    @else
                    <button id="mostrarRegistro">ÚNETE A COSMOS</button>
                    <button id="mostrarLogin">INICIAR SESIÓN</button>
                    @endif

                </div>
                <div class="logo-container">
                    <img src="{{ asset('images/logoCosmosCinema.png') }}" alt="Cosmos Cinema Logo" class="cinema-logo">
                </div>
            </section>

            <x-vistas.mostrar_peliculas :peliculas='$peliculas' :generos='$generos'/>

        </div>
    </section>

    <!-- Modal de compra de entradas oculto inicialmente -->
    <section id="seccionCompra" class="pt-5 hidden">

        <div>
            <div id='cerrar-button' class='header-buttons cerrar-button'>
                <button id="cerrarCompra">VOLVER</button>
            </div>

            <div class="movie-container">
                <label>Pick a Movie</label>
                <!-- Ya haremos el select con las querys necesarias para sacar info de base de datos -->
                <select id="movie">
                    <option value='8'>True Romance - $8</option>
                    <option value='8'>American History X - $8</option>
                    <option value='8'>A Beautiful Mind - $8</option>
                    <option value='10'>Joker - $10</option>
                </select>
            </div>

            <!-- Podemos meter los tres dentro de una tabla y juntarla a asiento-->
            <ul class="showcase">
                <li>
                    <div class="seat"></div>
                    <small>Available</small>
                </li>
                <li>
                    <div class="seat selected"></div>
                    <small>Selected</small>
                </li>
                <li>
                    <div class="seat occupied"></div>
                    <small>Occupied</small>
                </li>
            </ul>

            <!-- Contenedor para los asientos -->
            <div class="container">
                <div class="screen"></div>
                @for ($i = 0; $i < 8; $i++) <div class="row">
                    @for ($j = 0; $j < 8; $j++) <div class="seat">
            </div>
            @endfor
        </div>
        @endfor
        </div>

        <!-- Información de los asientos seleccionados y precio -->
        <div class="text-wrapper">
            <p class="text">
                You have selected <span id="count">0</span> seats for a price of $<span id="total">0</span>
            </p>
            <button id="confirmarCompra">Comprar Entradas</button>
        </div>
        </div>
    </section>



    <x-modal.modal_registro :ciudades='$ciudades'/>

    <x-modal.modal_login/>

    <x-modal.modal_usuario/>

    @stack('scripts')

</body>

</html>