<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProcesarPago extends Controller
{
    public function procesar_pago(Request $request) {
       
        // Unir todas las reglas
        $todas_las_reglas = array_merge(
            $this->obtener_reglas_pago(),
            $this->obtener_reglas_sesion(),
            $this->obtener_reglas_asientos()
        );

        // Unir todos los mensajes
        $todos_los_mensajes = array_merge(
            $this->obtener_mensajes_pago(),
            $this->obtener_mensajes_sesion(),
            $this->obtener_mensajes_asientos()
        );

        // Si el usuario está logueado, se recuperan su id, y se añaden sus reglas y mensajes
        if (Auth::check()) {
            $request->merge(['usuario_id_autenticado' => Auth::id()]);

            $todas_las_reglas = array_merge($todas_las_reglas, $this->obtener_reglas_usuario());
            $todos_los_mensajes = array_merge($todos_los_mensajes, $this->obtener_mensajes_usuario());
        }

        // Se usa Validator para comprobar mediante reglas establecidas si los valores son correctos
        // Validar las reglas establecidas con los valores introducidos. Recuperar mensajes de error correspondientes
        $validator = Validator::make($request->all(), $todas_las_reglas, $todos_los_mensajes);

        // Si hay errores, se devuelven los errores y se mantienen los valores introducidos
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'procesar_pago')   // Errores
                ->withInput();                              // Devolver input ya escrito
        }

        // Recuperar los datos validados
        $datos_validados = $validator->validated();


        // Comprobar que el precio total es correcto
        // Recuperar los datos del precio
        $precio_total_calculado = $datos_validados['precio_total'];
        $porcentaje_descuento = $datos_validados['precio_descuento'];
        $precio_final_esperado = $precio_total_calculado * (1 - ($porcentaje_descuento / 100));

        // Comparar con una pequeña tolerancia para evitar problemas con puntos flotantes
        $tolerancia = 0.01; // 1 céntimo de tolerancia

        if (abs($precio_final_esperado - $datos_validados['precio_final']) > $tolerancia) {
            // Log para indicar un posible fallo en el sistema de precios y descuentos
            Log::warning('Discrepancia en el cálculo del precio final.', [
                'precio_total' => $precio_total_calculado,
                'porcentaje_descuento' => $porcentaje_descuento,
                'precio_final_enviado' => $datos_validados['precio_final'],
                'precio_final_calculado_servidor' => $precio_final_esperado
            ]);

            return redirect()->back()
                ->withErrors(['calculo_precio' => 'El cálculo del precio final con el descuento aplicado no es correcto.'], 'procesar_pago')
                ->withInput();
        }

        // Si la validación es correcta, se cambia el estado de la validación
        $validatedData = $validator->validated();

        // Se intentan generar Entradas y Factura. Si hay algún error, return con errores
        $generarEntrada = new GenerarEntrada();
        if (!$generarEntrada->generar_entrada($datos_validados)) {
            $mensajeError = $generarEntrada->ultimoError ?: 
                'Hubo un problema procesando tu pedido. Por favor, inténtalo de nuevo o contacta con soporte.';
            
            return redirect()->back()
                ->withErrors(['generar_entrada_custom_error' => $mensajeError], 'procesar_pago')
                ->withInput();
        }

        // Mirar si esto hace falta con Redsys o no (quitar espacios al número de tarjeta)
        $validatedData['cardNumber'] = str_replace(' ', '', $validatedData['cardNumber']);

        // Introducir aquí la lógica de pago. Si es exitosa, se vuelve al principal
        // Quizás se puede hacer otro modal de éxito de pago mostrando una preview del pdf o algo

        // On success, you might redirect to a success page or return a success message
        return redirect()->route('principal') // Replace with your success route
            ->with('success', '¡Pago procesado exitosamente!');

    }



    // --- Métodos para definir reglas por sección ---
    private function obtener_reglas_pago(): array
    {
        return [
            'cardNumber' => ['required', 'string', 'regex:/^[\d\s]+$/', 'min:16', 'max:20'],
            'cardName'   => 'required|string|max:255',
            'cardMonth'  => 'required|digits:2|numeric|min:1|max:12',
            'cardYear'   => 'required|digits:4|numeric|min:' . date('Y'),
            'cardCvv'    => 'required|numeric|digits_between:3,4',
        ];
    }

    private function obtener_reglas_sesion(): array
    {
        return [
            'sesion_id'         => 'required|integer|exists:sesion_pelicula,id',
            'precio_total'      => 'required|numeric|min:1',
            'precio_descuento'  => 'required|numeric|min:0',
            'precio_final'      => 'required|numeric|min:1',
        ];
    }

    private function obtener_reglas_asientos(): array
    {
        return [
            'asiento'           => 'required|array|min:1',
            'asiento.*'         => 'required|integer|distinct|exists:asiento,id_asiento',
        ];
    }

    private function obtener_reglas_usuario(): array {
        return [
            'usuario_id'             => 'sometimes|integer|exists:users,id|same:usuario_id_autenticado',
            'usuario_id_autenticado' => 'required|integer|exists:users,id',
        ];
    }

    // --- Métodos para definir mensajes por sección ---
    private function obtener_mensajes_pago(): array
    {
        return [
            'cardNumber.required' => 'El número de tarjeta es obligatorio.',
            'cardNumber.regex'    => 'El número de tarjeta solo puede contener dígitos y espacios.',
            'cardNumber.min'      => 'El número de tarjeta es demasiado corto.',
            'cardNumber.max'      => 'El número de tarjeta es demasiado largo.',
            'cardName.required'   => 'El titular de la tarjeta es obligatorio.',
            'cardMonth.required'  => 'El mes de caducidad es obligatorio.',
            'cardMonth.digits'    => 'El mes debe tener 2 dígitos.',
            'cardMonth.min'       => 'El mes de caducidad no es válido.',
            'cardMonth.max'       => 'El mes de caducidad no es válido.',
            'cardYear.required'   => 'El año de caducidad es obligatorio.',
            'cardYear.digits'     => 'El año debe tener 4 dígitos.',
            'cardYear.min'        => 'El año de caducidad no puede ser anterior al actual.',
            'cardCvv.required'    => 'El CVV es obligatorio.',
            'cardCvv.numeric'     => 'El CVV debe ser numérico.',
            'cardCvv.digits_between' => 'El CVV debe tener entre 3 y 4 dígitos.',
        ];
    }

    private function obtener_mensajes_sesion(): array
    {
        return [
            'sesion_id.required'       => 'La información de la sesión es obligatoria.',
            'sesion_id.integer'        => 'La información de la sesión no es válida.',
            'sesion_id.exists'         => 'La sesión especificada no existe.',
            'precio_total.required'    => 'El precio total es obligatorio.',
            'precio_total.numeric'     => 'El precio total debe ser un número.',
            'precio_total.min'         => 'El precio total no puede ser negativo.',
            'precio_descuento.required'=> 'El precio con descuento es obligatorio.',
            'precio_descuento.numeric' => 'El precio con descuento debe ser un número.',
            'precio_descuento.min'     => 'El precio con descuento no puede ser negativo.',
            'precio_final.required'    => 'El precio final es obligatorio.',
            'precio_final.numeric'     => 'El precio final debe ser un número.',
            'precio_final.min'         => 'El precio final no puede ser negativo.',
        ];
    }

    private function obtener_mensajes_asientos(): array
    {
        return [
            'asiento.required'         => 'No se han seleccionado asientos.',
            'asiento.array'            => 'La selección de asientos no es válida.',
            'asiento.min'              => 'Debes seleccionar al menos un asiento.',
            'asiento.*.required'       => 'Uno de los asientos seleccionados no es válido.',
            'asiento.*.integer'        => 'Uno de los asientos seleccionados debe ser un número.',
            'asiento.*.distinct'       => 'No puedes seleccionar el mismo asiento varias veces.',
            'asiento.*.exists'         => 'Uno de los asientos seleccionados no existe o no está disponible.',
        ];
    }

    private function obtener_mensajes_usuario(): array
    {
        return [
            'usuario_id.integer'    => 'La información del usuario no es válida.',
            'usuario_id.exists'     => 'El usuario especificado no existe.',
            'usuario_id.same'       => 'La información del usuario no coincide con el usuario autenticado.',
            'usuario_id_autenticado.required' => 'Error interno: Falta el ID de usuario autenticado.',
            'usuario_id_autenticado.exists' => 'Error interno: El usuario autenticado no es válido.',
        ];
    }
}
