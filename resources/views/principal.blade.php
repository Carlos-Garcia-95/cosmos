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
        'resources/js/user.js',
        'resources/css/user_modal.css', 
        'resources/js/cartaCosmos.js',
        'resources/css/cartaCosmos.css',
        'resources/css/cartelera.css',
        'resources/css/detalle_pelicula.css',
        'resources/js/detalle_y_asientos.js',
        'resources/css/confirmar_seleccion.css',
        'resources/css/invitado.css',
        'resources/css/pago.css',
    ])
    <!-- Revisar Manera de introducir js y css en blade con vite(npm)-->

</head>

<body id="general">
    <div id='top'></div>
    <section class="cloneable">
        
        <div class="main">
            <section class="header-section">
                <!-- Header con botones -->
                <x-vistas.header/>
            </section>

            <!-- Slider horizontal de películas -->
            <x-vistas.mostrar_peliculas_slider :peliculas='$peliculas'/>
            
        </div>
    </section>

    <!-- Sección de Cartelera -->
    <section id="seccion_cartelera" class="seccion_cartelera">
        <!-- Cartelera -->
        <x-vistas.mostrar_peliculas_cartelera :peliculas='$peliculas'/>
    </section>

    <!-- Modal de compra de entradas oculto inicialmente -->
    <section id="seccionCompra" class="pt-5 hidden">
        <div class='cartelera-titulo'>
            <h3>Elige tus Asientos</h3>
        </div>
        <div class='separador_compra'></div>
        <div class="seccion-compra-contenido">
            <!-- Fechas y horas de sesiones de la película -->
            <div class='seccion_sesiones' id='seccion_sesiones'>
                <div class='seccion_sesiones_dias' id='seccion_sesiones_dias'></div>
                <div class='seccion_sesiones_horas' id='seccion_sesiones_horas'></div>
            </div>
            <!-- Datos de la sesión y asientos asociados a ella -->
            <div class='seccion_asientos' id='seccion_asientos'>
                <x-vistas.seleccion_asientos/>
            </div>
        </div>
    </section>

    <!-- Section para los menus -->
    <section id="seccionMenus" class="py-5">
        <x-vistas.mostrar_menu :menus='$menus'/>
    </section>

    <!-- Modal de registro nuevo usuario -->
    <x-modal.modal_registro :ciudades='$ciudades'/>

    <!-- Modal login de usuario -->
    <x-modal.modal_login/>

    <!-- Modal de ficha de usuario -->
    <x-modal.modal_usuario/>

    <!-- Modal de detalle de la película y COMPRAR ENTRADAS -->
    <x-modal.modal_detalle_pelicula/>

    <!-- Modal de Comprar como Invitado -->
    <x-modal.modal_comprar_como_invitado/>

    <!-- Modal de confirmar selección de asientos -->
    <x-modal.modal_confirmar_seleccion/>
    
    <!-- Modal de pago -->
    <x-modal.modal_pago/>

    @stack('scripts')

    

</body>

</html>