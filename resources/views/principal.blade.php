<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cosmos Cinema</title>

    <!--GSAPLaravel_8 No borrar, para entrar en GSAP. correo diegito866@gmail.com-->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/slider.css', 'resources/js/registro.js', 'resources/js/login.js', 'resources/js/entradas.js', 'resources/css/compraEntradas.css', 'resources/js/compraEntradas.js'])
    <!-- Revisar Manera de introducir js y css en blade con vite(npm)-->

</head>

<body>

    <section class="cloneable">
        <div class="overlay">
            <div class="overlay-inner">
                <div class="overlay-count-row">
                    <div class="count-column">
                        <h2 data-slide-count="step" class="count-heading">01</h2>
                    </div>
                    <div class="count-row-divider"></div>
                    <div class="count-column">
                        <h2 data-slide-count="total" class="count-heading">04</h2>
                    </div>
                </div>
                <div class="overlay-nav-row"><button aria-label="previous slide" data-slider="button-prev" class="button"><svg xmlns="http://www.w3.org/2000/svg" width="100%" viewbox="0 0 17 12" fill="none" class="button-arrow">
                            <path d="M6.28871 12L7.53907 10.9111L3.48697 6.77778H16.5V5.22222H3.48697L7.53907 1.08889L6.28871 0L0.5 6L6.28871 12Z" fill="currentColor"></path>
                        </svg>
                        <div class="button-overlay">
                            <div class="overlay-corner"></div>
                            <div class="overlay-corner top-right"></div>
                            <div class="overlay-corner bottom-left"></div>
                            <div class="overlay-corner bottom-right"></div>
                        </div>
                    </button><button aria-label="previous slide" data-slider="button-next" class="button"><svg xmlns="http://www.w3.org/2000/svg" width="100%" viewbox="0 0 17 12" fill="none" class="button-arrow next">
                            <path d="M6.28871 12L7.53907 10.9111L3.48697 6.77778H16.5V5.22222H3.48697L7.53907 1.08889L6.28871 0L0.5 6L6.28871 12Z" fill="currentColor"></path>
                        </svg>
                        <div class="button-overlay">
                            <div class="overlay-corner"></div>
                            <div class="overlay-corner top-right"></div>
                            <div class="overlay-corner bottom-left"></div>
                            <div class="overlay-corner bottom-right"></div>
                        </div>
                    </button></div>
            </div>
        </div>
        <div class="main">
            <section class="header-section">
                <div class="header-buttons" style="display: flex; align-items: center;">
                @if (session('mensaje'))
                    <div id="success-message" class="success-message" style="color: #e50914; font-weight: bold;">
                        {{ session('mensaje') }}
                    </div>

                    <script>
                        // Eliminar el mensaje automáticamente después de 2 segundos
                        setTimeout(function() {
                            const message = document.getElementById('success-message');
                            if (message) {
                                message.style.display = 'none';
                            }
                        }, 2000);
                    </script>
                @endif
                    <a href="#seccionCompra">
                        <button id="mostrarCompra">COMPRAR ENTRADAS</button>
                    </a> <!--Luego lo moveremos, lo de bajar con efecto hasta el div y donde colocarlo-->
                    <!-- TODO -> Dar funcionalidad a estos botones -->
                    <!-- TODO -> Almacenar y comprobar la sesión del usuario -->
                    @if(session('success'))
                        <button id="miCuenta">MI CUENTA</button>
                        <button id="logout">LOGOUT</button>
                    @else
                        <button id="mostrarRegistro">ÚNETE A COSMOS</button>
                        <button id="mostrarLogin">INICIAR SESIÓN</button>
                    @endif
                    
                </div>
            </section>
            
            <div class="slider-wrap">
                <div data-slider="list" class="slider-list">
                    <div data-slider="slide" class="slider-slide">
                        <div class="slide-inner"><img src="https://cdn.prod.website-files.com/674d847bf8e817966d307714/674d90f74ff2fe8b0b912b97_slide-1.avif" loading="lazy" sizes="(max-width: 479px) 100vw, 560px" alt="Abstract layout By FAKURIANDESIGN through Unsplash">
                            <div class="slide-caption">
                                <div class="caption-dot"></div>
                                <p class="caption">Layout nº001</p>
                            </div>
                        </div>
                    </div>
                    <div data-slider="slide" class="slider-slide active">
                        <div class="slide-inner"><img src="https://cdn.prod.website-files.com/674d847bf8e817966d307714/674d90f7cf52dd961b48a1e2_slide-2.avif" loading="lazy" alt="Abstract layout By FAKURIANDESIGN through Unsplash">
                            <div class="slide-caption">
                                <div class="caption-dot"></div>
                                <p class="caption">Layout nº002</p>
                            </div>
                        </div>
                    </div>
                    <div data-slider="slide" class="slider-slide">
                        <div class="slide-inner"><img src="https://cdn.prod.website-files.com/674d847bf8e817966d307714/674d90f7f7cce73267703347_slide-3.avif" loading="lazy" sizes="(max-width: 479px) 100vw, 560px" alt="Abstract layout By FAKURIANDESIGN through Unsplash">
                            <div class="slide-caption">
                                <div class="caption-dot"></div>
                                <p class="caption">Layout nº003</p>
                            </div>
                        </div>
                    </div>
                    <div data-slider="slide" class="slider-slide">
                        <div class="slide-inner"><img src="https://cdn.prod.website-files.com/674d847bf8e817966d307714/674d90f7ccfd203c82a46798_slide-4.avif" loading="lazy" sizes="(max-width: 479px) 100vw, 560px" alt="Abstract layout By FAKURIANDESIGN through Unsplash">
                            <div class="slide-caption">
                                <div class="caption-dot"></div>
                                <p class="caption">Layout nº004</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal de compra de entradas oculto inicialmente -->
    <section id="seccionCompra" class="pt-5 hidden">

        <div>
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

            <a href="#mostrarCompra"><button id="cerrarCompra" class="cerrar-btn">Cerrar</button></a>

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



    <x-modal.modal_registro :ciudades="$ciudades">

    </x-modal.modal_registro>

    <x-modal.modal_login>

    </x-modal.modal_login>


</body>

</html>