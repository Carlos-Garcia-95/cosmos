<?php

namespace App\Http\Controllers;

use App\Mail\EmailEntradas;
use App\Models\Asiento;
use App\Models\AsientoEstado;
use App\Models\Entrada;
use App\Models\Factura;
use App\Models\Fecha;
use App\Models\Hora;
use App\Models\Pelicula;
use App\Models\Sala;
use App\Models\SesionPelicula;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mail;
use Storage;
use Str;

class GenerarEntrada
{
    private array $datos_validados;

    private ?\Illuminate\Database\Eloquent\Collection $asientos;
    private ?SesionPelicula $sesion = null;
    private ?User $usuario = null;
    private ?Factura $factura = null;
    private ?array $entradas = null;
    private ?array $rutas_pdf = null;

    public ?string $ultimoError = null;

    public function generar_entrada($datos_validados) {
        // Recuperar datos
        $this->datos_validados = $datos_validados;

        // Se resetea el ultimoError por si hay que devolverlo
        $this->ultimoError = null;

        // Se inicia una nueva transacción
        DB::beginTransaction();

        try {

            if (!$this->recuperar_asientos()) {
                DB::rollBack();
                return false;
            }

            if (!$this->recuperar_sesion()) {
                DB::rollBack();
                return false;
            }
            
            if (isset($datos_validados["usuario_id"])) {
                if (!$this->recuperar_usuario()) {
                    DB::rollBack();
                    return false;
                }
            }

            if (!$this->generar_factura()) {
                DB::rollBack();
                return false;
            }

            if (!$this->generar_entradas()) {
                DB::rollBack();
                return false;
            }

            if (!$this->actualizar_asientos()) {
                DB::rollBack();
                return false;
            }

            if (!$this->generar_pdf()) {
                DB::rollBack();
                return false;
            }

            if (!$this->enviar_correo()) {
                DB::rollBack();
                return false;
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error crítico al generar entradas: ' . $e->getMessage(), [
                'datos' => $this->datos_validados,
                'exception_trace' => $e->getTraceAsString()
            ]);
            $this->ultimoError = 'Ocurrió un error inesperado durante el proceso. Por favor, contacta con soporte.';
            return false;
            
        }

        



    }

    private function recuperar_asientos(): bool {
        try {
            // Recuperar el id de Disponible
            $estado = AsientoEstado::where('estado', 'disponible')->first();

            // Si no se puede recuperar lanzamos un error
            if (!$estado) {
                $this->ultimoError = "No se pudo encontrar la definición del estado 'disponible'. Por favor, verifica la configuración del sistema.";
                Log::error($this->ultimoError);
                return false;
            }

            // Recuperar asientos por id
            $this->asientos = Asiento::whereIn('id_asiento', $this->datos_validados['asiento'])
                                           ->where('estado', $estado->id)
                                           ->lockForUpdate()        // Bloquear esos asientos hasta que termine la transacción
                                           ->get();

            // Comprobar que se han recuperado la cantidad de asientos correcta
            if ($this->asientos->count() !== count($this->datos_validados['asiento'])) {
                $this->ultimoError = "Algunos asientos seleccionados ya no están disponibles. Por favor, inténtelo de nuevo más tarde"; 
                Log::warning($this->ultimoError, ['solicitados' => $this->datos_validados['asiento'], 'encontrados' => collect($this->asientos)->pluck('id_asiento')->toArray()]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error recuperando asientos: " . $e->getMessage());
            $this->ultimoError = "Error al verificar la disponibilidad de los asientos.";
            return false;
        }
    }


    private function recuperar_sesion(): bool {
        try {
            // Recuperar asientos por id
            $this->sesion = SesionPelicula::find($this->datos_validados["sesion_id"]);

            // Comprobar que se han recuperado la sesión
            if (!$this->sesion) {
                $this->ultimoError = 'La sesión de película seleccionada ya no es válida o no existe.';
                Log::warning($this->ultimoError, [
                    'sesion_id_solicitada' => $this->datos_validados['sesion_id']
                ]);
                return false;
            }

            // Comprobar que la sesión está activa
            if (!$this->sesion->activa) {
                $this->ultimoError = 'La sesión de película seleccionada ya no está activa.';
                Log::warning($this->ultimoError, [
                    'sesion_id_solicitada' => $this->sesion->id,
                    'sesion_estado' => $this->sesion->activa
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error recuperando la sesión ID {$this->datos_validados['sesion_id']}: " . $e->getMessage());
            $this->ultimoError = "Error al cargar los datos de la sesión. Por favor, inténtalo más tarde.";
            return false;
        }
    }


    private function recuperar_usuario(): bool {

        
        // Comprobar si el usuario es invitado. Si es invitado usuario será null
        if (!isset($this->datos_validados['usuario_id']) || is_null($this->datos_validados['usuario_id'])) {
            $this->usuario = null;
            return true;
        }

        try {
            // Recuperar usuario por id
            $this->usuario = User::find($this->datos_validados["usuario_id"]);

            // Comprobar que se ha recuperado el usuario
            if (!$this->usuario) {
                $this->ultimoError = 'La información del usuario asociado a la compra no es válida o el usuario no existe.';
                Log::warning($this->ultimoError, [
                    'usuario_id_solicitado' => $this->datos_validados['usuario_id']
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error recuperando el usuario ID {$this->datos_validados['usuario_id']}: " . $e->getMessage());
            $this->ultimoError = "Error al cargar los datos del usuario. Por favor, inténtalo más tarde.";
            return false;
        }
    }


    private function generar_factura(): bool {
       
        // Se recupera el nº tarjeta, se quitan los espacios (si hay) y se reduce a los 4 últimos dígitos
        $numeroTarjetaSinEspacios = str_replace(' ', '', $this->datos_validados["cardNumber"]);
        $ultimos_digitos = substr($numeroTarjetaSinEspacios, -4);

        try {
            // TODO -> Mirar a ver si se puede arreglar el id_impuesto
            // Se crea la factura con los datos recuperados
            $this->factura = Factura::create([
                'monto_total' => $this->datos_validados["precio_final"],             
                'ultimos_digitos' => $ultimos_digitos,
                'titular' => $this->datos_validados["cardName"],                       
                'id_user' => $this->usuario ? $this->usuario->id : null, 
                'id_impuesto' => 1,
            ]);

            // Si no se genera correctamente, se lanza un error
            if (!$this->factura) {
                $this->ultimoError = "Error al crear el registro de la factura.";
                Log::error($this->ultimoError);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error generando la factura: " . $e->getMessage());
            $this->ultimoError = "Error al generar la factura. Por favor, inténtalo más tarde.";
            return false;
        }
    }


    private function generar_entradas(): bool {
        try {
            // Recuperar número de sala
            $sala = Sala::find($this->sesion->id_sala);

            if (!$sala) {
                $this->ultimoError = 'Hubo un error en la generación de entradas. Por favor, inténtelo más tarde.';
                Log::warning($this->ultimoError, [
                    'sala_id' => $this->sesion->id_sala, 'sala' => $sala
                ]);
                return false;
            }

            // Recuperar título de película
            $pelicula = Pelicula::find($this->sesion->id_pelicula);

            if (!$pelicula) {
                $this->ultimoError = 'Hubo un error en la generación de entradas. Por favor, inténtelo más tarde.';
                Log::warning($this->ultimoError, [
                    'pelicula_id' => $this->sesion->id_pelicula, 'pelicula' => $pelicula
                ]);
                return false;
            }
    
            // Se genera una entrada por cada asiento distinto que hay
            foreach ($this->asientos as $asiento) {
                // Generar un código QR por cada entrada
                $codigo_qr = $this->generar_codigo_qr();

                // Si no se genera correctamente, se lanza un error
                if (!$codigo_qr) {
                    $this->ultimoError = 'Hubo un error en la generación de entradas. Por favor, inténtelo más tarde.';
                    Log::warning($this->ultimoError, [
                        'codigo_qr_generado' => $codigo_qr
                    ]);
                    return false;
                }

                // Calcular precios y descuentos de cada entrada
                $precio_total = 10;
                $porcentaje_descuento = $this->datos_validados['precio_descuento'];
                $precio_final = $precio_total * (1 - ($porcentaje_descuento / 100));

                // TODO -> Ver que se puede hacer con tipo_entrada
                // TODO -> Ver si se puede arreglar id_sala para que sea un sala_numero o algo así
                // TODO -> Ver lo del los precios de las entradas (sacarlo de BBDD)
                $entrada = Entrada::create([
                    'codigo_qr' => $codigo_qr,
                    'ruta_pdf' => "",
                    'precio_total' => $precio_total,
                    'descuento' => $porcentaje_descuento,
                    'precio_final' => $precio_final,
                    'sala' => $sala->id_sala,
                    'sala_id' => $this->sesion->id_sala,
                    'poster_ruta' => $pelicula->poster_ruta,
                    'pelicula_titulo' => $pelicula->titulo,
                    'pelicula_id' => $this->sesion->id_pelicula,
                    'hora' => $this->sesion->hora,
                    'fecha' => $this->sesion->fecha,
                    'asiento_id' => $asiento->id_asiento,
                    'asiento_fila' => $asiento->fila,
                    'asiento_columna' => $asiento->columna,
                    'usuario_id' => $this->usuario ? $this->usuario->id : null,
                    'factura_id' => $this->factura->id_factura,
                    'tipo_entrada' => 1,
                ]);

                if (!$entrada) {
                    $this->ultimoError = 'Hubo un error en la generación de entradas. Por favor, inténtelo más tarde.';
                    Log::warning($this->ultimoError, [
                        'entrada' => $entrada
                    ]);
                    return false;
                };

                $this->entradas[] = $entrada;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error generando las entradas: " . $e->getMessage());
            $this->ultimoError = "Error al generar las entradas. Por favor, inténtalo más tarde.";
            return false;
        }
    }


    private function actualizar_asientos(): bool {
        try {

            // Comprobar que hay asientos
            if ($this->asientos->isEmpty()) {
                Log::info("No hay asientos para actualizar su estado.", ['datos_validados' => $this->datos_validados]);
                $this->ultimoError = "Error al actualizar los asientos. Por favor, inténtalo más tarde.";
                return false;
            }

            // Se guardan los ids de los asientos en un array
            $asientos_id = $this->asientos->pluck('id_asiento')->toArray();

            // Se establece el id de estado 'Ocupado'
            $ocupado = AsientoEstado::where('estado', 'ocupado')->first();

            if (!$ocupado) {
                Log::info("Error al recuperar el estado 'ocupado' del asiento.", ['datos_validados' => $this->datos_validados]);
                $this->ultimoError = "Error al actualizar los asientos. Por favor, inténtalo más tarde.";
                return false;
            }

            // Se actualizan todos los id de asientos recogidos al id ocupado
            $filas_afectadas = Asiento::whereIn('id_asiento', $asientos_id)
                                        ->update(['estado' => $ocupado->id]);

            if ($filas_afectadas !== $this->asientos->count()) {
                $this->ultimoError = "No se pudieron actualizar todos los asientos. Por favor, inténtalo más tarde.";
                Log::warning($this->ultimoError, [
                    'ids_a_actualizar' => $asientos_id,
                    'filas_afectadas_db' => $filas_afectadas
                ]);
                return false; 
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error generando las entradas: " . $e->getMessage());
            $this->ultimoError = "Error al actualizar los asientos. Por favor, inténtalo más tarde.";
            return false;
        }
    }


    private function generar_pdf(): bool {
        
        try {
            // Se generan los pdf y se guardan todas las ruta de los pdf generados (para luego mandarlos)
            foreach ($this->entradas as $entrada) {
                $pdf_ruta = $this->crear_pdf($entrada);

                if (!$pdf_ruta) {
                    Log::error("Error generando los pdf de las entradas: " . $entrada->id);
                    $this->ultimoError = "Error al generar las entradas. Por favor, inténtalo más tarde.";
                    return false;
                }

                $this->rutas_pdf[] = $pdf_ruta;

                Entrada::where('id_entrada', $entrada->id_entrada)
                            ->update(['ruta_pdf' => $pdf_ruta]);

            }

            // Comprobar que se han generado tantos pdf como entradas entradas
            if (count($this->rutas_pdf) !== count($this->entradas)) {
                    Log::error("Error generando los pdf de las entradas: " . $this->rutas_pdf);
                    $this->ultimoError = "Error al generar las entradas. Por favor, inténtalo más tarde.";
                    return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Error generando los pdf de las entradas: " . $e->getMessage());
            $this->ultimoError = "Error al generar los pdf. Por favor, inténtalo más tarde.";
            return false;
        }
    }


    

    
    private function enviar_correo(): bool {
        
        $email_destino = null;
        $email_usuario = null;

        // Si es usuario registrado
        // TODO -> Email invitado
        if ($this->usuario) {
            $email_destino = $this->usuario->email;
            $email_usuario = $this->usuario;
        } elseif (!empty($this->datos_validados['email_invitado'])) { // Si es invitado
            $email_destino = $this->datos_validados['email_invitado'];
            // Crear un objeto de "mentira" User o pasar los datos del invitado al Mailable
            $email_usuario = new User(['name' => $this->datos_validados['cardName'] ?? 'Cliente']);
        }

        // Si no hay email de destino se lanza un error
        if (is_null($email_destino)) {
            Log::info([
                "mensaje" => "No se envía correo: no hay destinatario (usuario no registrado o sin email de contacto).",
                "email" => $email_destino,
                "usuario" => $this->usuario
            ]);
            $this->ultimoError = "Error al enviar el correo. Por favor, inténtalo más tarde.";
            return false;
        }

        // Si no hay pdf generado/s, se lanza un error
        if (empty($this->rutas_pdf)) {
            Log::warning("No se envía correo: no hay PDFs de entradas para adjuntar.", ['email_destino' => $email_destino]);
             $this->ultimoError = "Error al enviar el correo. Por favor, inténtalo más tarde.";
            return false; // Por ahora, si no hay PDFs, no lo consideramos un error de envío de correo.
        }

        try {  
            // Guardar las rutas de los pdf generados
            $rutas_pdf = [];
            foreach ($this->rutas_pdf as $ruta_pdf) {
                $rutas_pdf[] = Storage::disk('public')->path($ruta_pdf);
            }

            // Crear instancia de Mailable (EmailEntradas)
            $correo_confirmacion = new EmailEntradas($email_usuario, $this->factura, $rutas_pdf);

            // Enviar el correo con Mail. También lo ponemos en cola
            // Al ponerlo en cola, la aplicación seguirá funcionando (para el usuario) mientras por detrás se manda el email
            Mail::to($email_destino)->queue($correo_confirmacion);

            Log::info("Correo de confirmación de compra enviado a: " . $email_destino . " con " . count($rutas_pdf) . " entradas adjuntas.");
            return true;
        } catch (\Exception $e) {
            Log::error("Error enviado los pdf al correo del usuario: " . $e->getMessage());
            $this->ultimoError = "Error al enviar el correo. Por favor, inténtalo más tarde.";
            return false;
        }

        return true;
    }


    private function generar_codigo_qr(): string {
        // Se utiliza una herramienta Str para generar un String de 128 bits único (prácticamente imposible repetir)
        $codigo_unico = 'ENTRADA-' . Str::uuid()->toString();
        
        return $codigo_unico;
    }


    private function crear_pdf(Entrada $entrada): ?string {
        try {
            if (!$entrada) {
                return null;
            }

            // Crear objeto con datos de empresa
            $empresa = (object) [
                'nombre_legal' => config('company.name', 'Cosmos Cinema (Test)'),
                'cif' => config('company.cif', 'B99999999'),
            ];

            // Recuperar fecha
            $fecha_entrada = Fecha::find($entrada->fecha);
            if (!$fecha_entrada) {
                Log::error("No se pudo guardar recuperar la fecha de la entrada: " . $entrada->fecha);
                return null;
            }

            // Recuperar hora
            $hora_entrada = Hora::find($entrada->hora);
            if (!$hora_entrada) {
                Log::error("No se pudo guardar recuperar la hora de la entrada: " . $entrada->hora);
                return null;
            }

            // Cargar la vista Blade para la entrada
            $pdf = PDF::loadView('pdf.entrada_cine', compact('entrada', 'empresa', 'fecha_entrada', 'hora_entrada'));

            // Se crea un nombre de archivo único
            $nombre_archivo = 'entrada-' . $entrada->id_entrada . '-' . Str::random(10) . '.pdf';

            // Se crea la ruta relativa
            $ruta_relativa = 'entradas_pdf/' . $nombre_archivo;

            // Se guarda el pdf
            $exito_guardado = Storage::disk('public')->put($ruta_relativa, $pdf->output());

            if (!$exito_guardado) {
                Log::error("No se pudo guardar el PDF en el disco 'public': " . $ruta_relativa);
                return null;
            }

            return $ruta_relativa;

        } catch (\Exception $e) {
            Log::error("Error generando PDF para entrada ID {$entrada->id_entrada}: " . $e->getMessage(), ['exception' => $e]);
            // No setear $this->ultimoError aquí directamente, dejar que el método llamador lo haga
            // si este fallo es crítico para el proceso general.
            return null;
        }
    }
}
