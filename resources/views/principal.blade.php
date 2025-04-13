<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cosmos Cinema</title>

    <!--GSAPLaravel_8 No borrar, para entrar en GSAP. correo diegito866@gmail.com-->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/slider.js', 'resources/css/slider.css'])<!-- Revisar Manera de introducir js y css en blade con vite(npm)-->

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
                <div class="header-buttons">
                    <button id="mostrarRegistro">ÚNETE A COSMOS</button></a>
                    <a><button>INICIAR SESIÓN</button></a>
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
    <div class="modal-registro" id="modalRegistro">
        <div class="form-container">
            <button class="close-button" id="cerrarRegistro">&times;</button>
            <div class="logo-container">
                <img src="{{ asset('images/logoCosmosCinema.png') }}" alt="Cosmos Cinema Logo" class="logo">
            </div>
            <form method="POST">
                @csrf

                <div id="step1" class="form-step active">
                    <!-- Fila para el email -->
                    <div class="form-row">
                        @if ($errors->has('email'))
                        <div class="error-message">{{ $errors->first('email') }}</div>
                        @endif
                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required class="input">
                    </div>

                    <!-- Fila para confirmar el email -->
                    <div class="form-row">
                        @if ($errors->has('email_confirmation'))
                        <div class="error-message">{{ $errors->first('email_confirmation') }}</div>
                        @endif
                        <input type="email" name="email_confirmation" placeholder="Confirmar tu email" value="{{ old('email_confirmation') }}" required class="input">
                    </div>

                    <!-- Fila para nombre y apellidos -->
                    <div class="form-row">
                        <div class="half-width">
                            @if ($errors->has('nombre'))
                            <div class="error-message">{{ $errors->first('nombre') }}</div>
                            @endif
                            <input type="text" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}" required class="input">
                        </div>
                        <div class="half-width">
                            @if ($errors->has('apellidos'))
                            <div class="error-message">{{ $errors->first('apellidos') }}</div>
                            @endif
                            <input type="text" name="apellidos" placeholder="Apellidos" value="{{ old('apellidos') }}" required class="input">
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="button-group">
                        <a href="{{ route('principal') }}" class="btn back-button">Volver</a>
                        <button type="button" class="btn next-step">Siguiente</button>
                    </div>
                </div>

                <div id="step2" class="form-step">
                    @if ($errors->has('direccion'))
                    <div class="error-message">{{ $errors->first('direccion') }}</div>
                    @endif
                    <input type="text" name="direccion" placeholder="Dirección" value="{{ old('direccion') }}" required class="input">
                    @if ($errors->has('ciudad'))
                    <div class="error-message">{{ $errors->first('ciudad') }}</div>
                    @endif
                    <input type="text" name="ciudad" placeholder="Ciudad" value="{{ old('ciudad') }}" required class="input">
                    @if ($errors->has('codigo_postal'))
                    <div class="error-message">{{ $errors->first('codigo_postal') }}</div>
                    @endif
                    <input type="text" name="codigo_postal" placeholder="Código Postal" value="{{ old('codigo_postal') }}" required class="input">
                    @if ($errors->has('telefono'))
                    <div class="error-message">{{ $errors->first('telefono') }}</div>
                    @endif
                    <input type="tel" name="telefono" placeholder="Teléfono" value="{{ old('telefono') }}" required class="input">
                    @if ($errors->has('password'))
                    <div class="error-message">{{ $errors->first('password') }}</div>
                    @endif
                    <input type="password" name="password" placeholder="Contraseña" required class="input">
                    <div class="button-group">
                        <button type="button" class="btn prev-step">Anterior</button>
                        <button type="button" class="btn next-step">Siguiente</button>
                    </div>
                </div>

                <div id="step3" class="form-step">
                    <label><input type="checkbox"> Acepto recibir publicidad y comunicaciones sobre promociones y novedades relacionadas con mis preferencias y gustos cinematográficos.</label>
                        @if ($errors->has('mayor_edad'))
                        <div class="error-message">{{ $errors->first('mayor_edad') }}</div>
                        @endif
                        <label><input type="checkbox" name="mayor_edad" {{ old('mayor_edad') ? 'checked' : '' }} required> Soy mayor de 14 años</label>
                        <div class="button-group">
                            <button type="button" class="btn prev-step">Anterior</button>
                            <button type="submit" class="btn">Registrarse</button>
                        </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>