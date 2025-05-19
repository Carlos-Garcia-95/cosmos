<?php

namespace App\Http\Controllers;

use App\Models\Asiento;
use App\Models\Entrada;
use App\Models\Factura;
use App\Models\SesionPelicula;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerarEntrada
{
    private array $datos_validados;

    private array $asientos;
    private ?SesionPelicula $sesion = null;
    private ?User $usuario = null;

    public ?string $ultimoError = null;

    public function generar_entrada($datos_validados) {
        return true;
        
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

            if (!$this->guardar_entrada()) {
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
            // Recuperar asientos por id
            $this->asientos = Asiento::whereIn('id_asiento', $this->datos_validados['asiento'])
                                           ->where('estado', 'disponible')
                                           ->lockForUpdate()        // Bloquear esos asientos hasta que termine la transacción
                                           ->get();

            // Comprobar que se han recuperado la cantidad de asientos correcta
            if (count($this->asientos) !== count($this->datos_validados['asiento'])) {
                $this->ultimoError = 'Algunos de los asientos seleccionados ya no están disponibles o no existen. Por favor, selecciona otros.';
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
        try {
            // Recuperar asientos por id
            $this->usuario = SesionPelicula::find($this->datos_validados["usuario_id"]);

            // Comprobar que se han recuperado la sesión
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
            Factura::create([
                'nombre' => $this->datos_validados["precio_final"],                     
                'ultimos_digitos' => $ultimos_digitos,               
                'titular' => $this->datos_validados["cardName"],                       
                'id_user' => $this->usuario->id, 
                'id_impuesto' => 1,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error generando la factura: " . $e->getMessage());
            $this->ultimoError = "Error al generar la factura. Por favor, inténtalo más tarde.";
            return false;
        }
    }


    private function guardar_entrada(): bool {
        /* $this->precio_array["precio_total"] = $datos_validados["precio_total"];
        $this->precio_array["precio_descuento"] = $datos_validados["precio_descuento"];
        $this->precio_array["precio_final"] = $datos_validados["precio_final"]; */

        $codigo_qr = $this->generar_codigo_qr();
        return true;

        Entrada::create([

        ]);
    }


    private function actualizar_asientos(): bool {

        return true;
    }


    private function generar_pdf(): bool {
        return true;

    }


    private function enviar_correo(): bool {
        return true;

    }


    private function generar_codigo_qr(): bool {
        return true;

    }
}
