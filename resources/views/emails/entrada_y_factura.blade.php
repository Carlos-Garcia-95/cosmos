<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tus Entradas y Factura</title>
    <style>
        /* ESTILOS CSS EN LÍNEA (IMPORTANTE PARA EMAILS) */
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .factura-container { width: 100%; max-width: 600px; margin: 0 auto; }
        .factura-header { border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 10px; }
        .factura-header h1 { font-size: 18px; margin: 0; }
        .factura-header p { margin: 2px 0; }
        .entradas-table { width: 100%; border-collapse: collapse; }
        .entradas-table th, .entradas-table td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        .entradas-table th { background-color: #f0f0f0; }
        .factura-resumen { text-align: right; margin-top: 10px; }
        .factura-resumen p { margin: 2px 0; }
        .factura-footer { text-align: center; margin-top: 20px; font-size: 10px; color: #777; }
    </style>
</head>
<body>

    <p>Estimado/a {{ $data['usuario']->name }},</p>

    <p>Gracias por tu compra en Cosmos Cinema. Adjuntamos tus entradas y la factura en formato PDF.</p>

    <h2>Resumen de la Factura</h2>
    <p>Número de Factura: {{ $data['factura']->num_factura }}</p>
    <p>Total: {{ number_format($data['factura']->monto_total, 2, ',', '.') }} €</p>

    <h2>Detalle de Entradas</h2>
    <ul>
        @foreach($data['entradas'] as $entrada)
            <li>{{ $entrada->pelicula_titulo }} - {{ $entrada->hora }} - Butaca: {{ $entrada->asiento_fila }}{{ $entrada->asiento_columna }} - {{ number_format($entrada->precio_final, 2, ',', '.') }} €</li>
        @endforeach
    </ul>

    <p>¡Gracias por tu compra!</p>

</body>
</html>