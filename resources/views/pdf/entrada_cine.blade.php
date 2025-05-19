<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Entrada Cosmos Cinema - {{ $entrada->pelicula_titulo }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        @page {
            margin: 7mm;
            /* Aumentamos un poco el margen de la página */
            size: 180mm 95mm;
            /* Ancho x Alto */
        }

        .ticket {
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            border: 0.5px solid #555;
            overflow: hidden;
            position: relative;
            /* Para el pseudo-elemento de la línea perforada */
            display: flex;
            flex-direction: row;
            box-sizing: border-box;
            /* Asegurarnos que el padding/border del ticket no lo agrande */
        }

        /* Línea perforada vertical entre .ticket-main y .ticket-stub */
        .ticket::before {
            content: '';
            position: absolute;
            left: 69.5%;
            /* Coincide con el width de .ticket-main */
            top: 7mm;
            /* Aumentamos un poco el espacio superior e inferior */
            bottom: 7mm;
            width: 0;
            border-left: 1px dashed #999;
            z-index: 5;
        }

        .ticket-main {
            width: 68%;
            padding: 8px 10px;
            /* AUMENTADO PADDING INTERNO */
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            /* border-right: none; Ya no es necesaria por el ::before */
        }

        .ticket-stub {
            width: 32%;
            padding: 8px 8px;
            /* AUMENTADO PADDING INTERNO */
            box-sizing: border-box;
            text-align: center;
            background-color: #f8f8f8;
            /* Un poco más claro */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            /* Distribuye el espacio, QR arriba, ID abajo */
        }

        .ticket-header {
            text-align: center;
            margin-bottom: 6px;
            /* Más espacio */
            padding-bottom: 4px;
            border-bottom: 0.5px solid #e8e8e8;
            width: 100%;
            flex-shrink: 0;
        }

        .ticket-header .cinema-logo {
            font-weight: bold;
            font-size: 9.5pt;
            color: #222;
            letter-spacing: 0.5px;
        }

        .ticket-header h1 {
            /* "ENTRADA" */
            margin: 2px 0 0 0;
            font-size: 10.5pt;
            color: #111;
            font-weight: bold;
            text-transform: uppercase;
        }

        .movie-banner {
            text-align: center;
            margin-bottom: 10px;
            /* Más espacio */
            width: 100%;
            flex-shrink: 0;
        }

        .movie-banner h2 {
            /* Título de la película */
            margin: 0;
            font-size: 13pt;
            /* Ligeramente más pequeño si era muy grande */
            color: #000;
            font-weight: bold;
            line-height: 1.25;
            word-break: break-word;
        }

        .movie-and-session-info-block {
            margin-bottom: 8px;
            /* Más espacio */
            flex-grow: 1;
            /* Sigue siendo importante para la distribución vertical */
        }

        .movie-and-session-info-block::after {
            content: "";
            display: table;
            clear: both;
        }

        .movie-poster-container {
            float: left;
            width: 65px;
            /* Ajustar si es necesario */
            height: 95px;
            /* Ajustar si es necesario */
            margin-right: 10px;
            /* Espacio a la derecha del poster */
            margin-bottom: 5px;
            /* Espacio debajo del poster si el texto de detalles es más alto */
            overflow: hidden;
            border: 0.5px solid #ccc;
            box-sizing: border-box;
        }

        .poster-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .poster-placeholder {
            width: 100%;
            height: 100%;
            background: #f0f0f0;
            /* Un poco más oscuro que el fondo del stub */
            display: table;
            font-size: 7pt;
            color: #777;
            box-sizing: border-box;
        }

        .poster-placeholder span {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 3px;
        }

        .session-details-container {
            float: left;
            /* Debe flotar para estar al lado del poster */
            width: calc(100% - 65px - 10px);
            /* ANCHO CALCULADO: 100% - ancho_poster - margen_poster */
            font-size: 7.5pt;
            /* Un poco más grande */
            box-sizing: border-box;
        }

        .session-details-container .detail-item {
            margin-bottom: 2.5px;
            /* Más espacio entre items */
            line-height: 1.2;
        }

        .session-details-container .detail-item .label {
            font-weight: bold;
            color: #333;
            /* Más oscuro */
            display: inline-block;
            width: 40px;
            /* Ajusta si es necesario */
            margin-right: 4px;
            vertical-align: top;
        }

        .session-details-container .detail-item .value {
            color: #000;
            /* Texto del valor más oscuro */
            display: inline;
        }

        hr.content-divider {
            border: none;
            border-top: 0.5px solid #ccc;
            /* Línea más visible */
            margin-top: auto;
            margin-bottom: 6px;
            /* Más espacio */
            width: 100%;
            flex-shrink: 0;
            clear: both;
        }

        .price-info {
            text-align: center;
            font-size: 8.5pt;
            margin-bottom: 6px;
            width: 100%;
            flex-shrink: 0;
        }

        .price-info strong {
            font-size: 9.5pt;
            color: #000;
        }

        .price-info span {
            display: block;
            font-size: 6.5pt;
            color: #666;
        }

        .ticket-info-footer {
            font-size: 6pt;
            /* Ligeramente más grande */
            color: #777;
            text-align: center;
            width: 100%;
            padding-top: 3px;
            border-top: 0.5px dotted #ddd;
            margin-top: auto;
            flex-shrink: 0;
            line-height: 1.2;
        }

        .ticket-info-footer p {
            margin: 0;
        }

        /* Estilos para el Talón (Stub) - .ticket-stub */
        .stub-header {
            font-size: 7pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            /* Más espacio */
            text-transform: uppercase;
            width: 100%;
            flex-shrink: 0;
            /* Para que no se encoja */
        }

        .stub-movie-title {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 6px;
            line-height: 1.2;
            width: 100%;
            word-break: break-word;
            flex-shrink: 0;
        }

        .stub-session,
        .stub-seat {
            font-size: 7.5pt;
            margin-bottom: 6px;
            width: 100%;
            flex-shrink: 0;
        }

        .qr-code-area {
            margin-top: 10px;
            margin-bottom: 8px;
            flex-grow: 1;
            /* Hace que el QR intente ocupar el espacio central verticalmente */
            display: flex;
            /* Para centrar el QR dentro de este espacio */
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .qr-code-area img,
        .qr-code-area svg {
            display: block;
            /* margin: 0 auto; Ya no es necesario por flex en el padre */
            width: 65px;
            /* QR un poco más grande */
            height: 65px;
        }

        .ticket-id-stub {
            font-size: 6pt;
            color: #666;
            /* margin-top: auto; Ya no es necesario por justify-content: space-between en .ticket-stub */
            width: 100%;
            padding-bottom: 3px;
            flex-shrink: 0;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <div class="ticket-main"> {{-- Este es el contenedor izquierdo --}}
            <header class="ticket-header">
                <div class="cinema-logo">
                    <span>COSMOS CINEMA</span>
                </div>
                <h1>ENTRADA</h1>
            </header>

            <section class="movie-banner"> {{-- ESTA SECCIÓN DEBE ESTAR AQUÍ --}}
                <h2>{{ Str::limit($entrada->pelicula_titulo, 35) }}</h2> {{-- Acortar un poco más si es necesario --}}
            </section>
            <section class="movie-and-session-info-block">
                <div class="movie-poster-container">
                    @if($entrada->pelicula && $entrada->pelicula->poster_ruta && Str::startsWith($entrada->pelicula->poster_ruta, ['http://', 'https://']))
                    <img src="{{ $entrada->pelicula->poster_ruta }}" alt="Poster {{ $entrada->pelicula_titulo }}" class="poster-image">
                    @else
                    <div class="poster-placeholder"><span>Poster no disponible</span></div>
                    @endif
                </div>

                <div class="session-details-container">
                    <div class="detail-item"><span class="label">SALA:</span> <span class="value">{{ $entrada->sala_id }}</span></div>
                    <div class="detail-item"><span class="label">FECHA:</span> <span class="value">{{ \Carbon\Carbon::parse($entrada->fecha)->format('d/m/Y') }}</span></div>
                    <div class="detail-item"><span class="label">HORA:</span> <span class="value">{{ \Carbon\Carbon::parse($entrada->hora)->format('H:i') }}</span></div>
                    <div class="detail-item"><span class="label">FILA:</span> <span class="value">{{ $entrada->asiento_fila }}</span></div>
                    <div class="detail-item"><span class="label">ASIENTO:</span> <span class="value">{{ $entrada->asiento_columna }}</span></div>
                    <div class="detail-item"><span class="label">TIPO:</span> <span class="value">{{ $entrada->tipoEntrada->nombre ?? 'General' }}</span></div>
                </div>
                <div style="clear: both; height: 0; line-height: 0;"></div> {{-- Clearfix más explícito --}}
            </section>

            <hr class="content-divider">

            <section class="price-info">
                <strong>PRECIO: {{ number_format($entrada->precio_final, 2, ',', '.') }} €</strong>
                @if($entrada->descuento > 0)
                <span>(Precio Original: {{ number_format($entrada->precio_total, 2, ',', '.') }} €, Desc: {{ $entrada->descuento }}%)</span>
                @endif
            </section>

            <footer class="ticket-info-footer">
                <p>ID Entrada: {{ $entrada->id_entrada }}</p>
                <p>Presenta esta entrada en el acceso. No reembolsable.</p>
            </footer>
        </div>

        <!-- <div class="ticket-stub">
            <div class="stub-header">
                <span>COSMOS CINEMA</span>
            </div>
            <div class="stub-movie-title">
                {{ Str::limit($entrada->pelicula_titulo, 25) }}
            </div>
            <div class="stub-session">
                {{ \Carbon\Carbon::parse($entrada->fecha)->format('d M') }} - {{ \Carbon\Carbon::parse($entrada->hora)->format('H:i') }}
            </div>
            <div class="stub-seat">
                S:{{ $entrada->sala_id }} F:{{ $entrada->asiento_fila }} A:{{ $entrada->asiento_columna }}
            </div>
            <div class="qr-code-area">
                {!! QrCode::size(90)->generate($entrada->codigo_qr) !!}
            </div>
            <div class="ticket-id-stub">
                ID: {{ $entrada->id_entrada }}
            </div> -->
        <!-- </div> -->
    </div>
</body>

</html>