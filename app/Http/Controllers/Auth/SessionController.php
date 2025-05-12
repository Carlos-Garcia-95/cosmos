<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pelicula;
use App\Models\Sesion;
use App\Models\Sala;
use App\Models\Hora;
use App\Models\Fecha;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException; // Importa la clase ValidationException

class SessionController extends Controller
{
    /**
     * Devuelve la lista de películas activas y en cartelera para el select.
     */
    public function getPeliculasActivasEnCartelera()
    {
        $peliculas = Pelicula::where('activa', 1)
            ->where('estreno', 0)
            ->get(['id', 'titulo']);
        return response()->json($peliculas);
    }

    /**
     * Devuelve las horas disponibles para una fecha, película y sala dadas.
     */
    public function getHorasDisponibles(Request $request)
    {
        $request->validate([
            'fecha_id' => ['required', 'exists:fecha,id'],
            'pelicula_id' => ['required', 'exists:pelicula,id'],
            'sala_id' => ['required', 'exists:sala,id_sala'], // Asumo que la PK de 'sala' es 'id_sala'.
        ], [
            'fecha_id.required' => 'La fecha es obligatoria.',
            'fecha_id.exists' => 'La fecha seleccionada no es válida.',
            'pelicula_id.required' => 'La película es obligatoria.',
            'pelicula_id.exists' => 'La película seleccionada no es válida.',
            'sala_id.required' => 'La sala es obligatoria.',
            'sala_id.exists' => 'La sala seleccionada no es válida.',
        ]);

        $fechaId = $request->input('fecha_id');
        $salaId = $request->input('sala_id');
        $peliculaId = $request->input('pelicula_id');

        $peliculaSeleccionada = Pelicula::findOrFail($peliculaId);
        $duracionPelicula = $peliculaSeleccionada->duracion; // Duración en minutos.
        $margenSeguridad = 10; // Minutos de limpieza o transición.

        $fechaObj = Fecha::find($fechaId);
        if (!$fechaObj) {
             return response()->json(['error' => 'Fecha no encontrada'], 400);
        }
        $fechaSeleccionadaStr = $fechaObj->fecha; // Fecha en formato YYYY-MM-DD.

        // Obtener sesiones existentes para la fecha y sala especificadas con relaciones cargadas
        $sesionesExistentes = Sesion::with(['horaSession', 'pelicula'])
                                    ->where('fecha', $fechaId)
                                    ->where('id_sala', $salaId)
                                    ->orderBy('hora')
                                    ->get();

        // Pre-calcular los rangos de tiempo de las sesiones ya ocupadas
        $rangosSesionesOcupadas = [];
        foreach ($sesionesExistentes as $sesion) {
            if ($sesion->horaSession && $sesion->pelicula) {
                $inicioSesionExistente = Carbon::parse($fechaSeleccionadaStr . ' ' . $sesion->horaSession->hora);

                // Calcula la hora de fin de la sesión existente, incluyendo duración y margen.
                $finSesionExistente = (clone $inicioSesionExistente)->addMinutes($sesion->pelicula->duracion + $margenSeguridad);

                $rangosSesionesOcupadas[] = [
                    'start' => $inicioSesionExistente,
                    'end' => $finSesionExistente
                ];
            }
        }

        // Obtener todas las horas posibles del sistema
        $horasPosiblesObjetos = Hora::orderBy('hora')->get();
        $horasDisponiblesConId = [];

        // Iterar sobre cada hora posible y determinar su disponibilidad
        foreach ($horasPosiblesObjetos as $horaObj) {
            $horaPosibleStr = $horaObj->hora;

            // Calcula el rango de tiempo para la nueva sesión propuesta.
            $inicioNuevaSesion = Carbon::parse($fechaSeleccionadaStr . ' ' . $horaPosibleStr);
            $finNuevaSesion = (clone $inicioNuevaSesion)->addMinutes($duracionPelicula + $margenSeguridad);

            $isAvailable = true;

            // Comprobar solapamiento con cada sesión existente
            foreach ($rangosSesionesOcupadas as $rangoOcupado) {
                // Condición de solapamiento: (inicioNueva < finOcupada) AND (finNueva > inicioOcupada)
                if ($inicioNuevaSesion < $rangoOcupado['end'] && $finNuevaSesion > $rangoOcupado['start']) {
                    $isAvailable = false;
                    break;
                }
            }

            // Opcional: No mostrar horas ya pasadas si la fecha es hoy.
            if ($fechaSeleccionadaStr == Carbon::now()->toDateString() && $inicioNuevaSesion->lt(Carbon::now())) {
                 $isAvailable = false;
            }


            if ($isAvailable) {
                $horasDisponiblesConId[] = [
                    'id' => $horaObj->id,
                    'text' => substr($horaPosibleStr, 0, 5), // Formato HH:MM
                ];
            }
        }

        return response()->json($horasDisponiblesConId);
    }

    /**
     * Guarda una nueva sesión en la base de datos.
     */
    public function storeSesion(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'fecha' => ['required', 'exists:fecha,id'],
                'sala_id' => ['required', 'exists:sala,id_sala'], // Asumo que la PK de 'sala' es 'id_sala'.
                'pelicula_id' => ['required', 'exists:pelicula,id'],
                'hora' => ['required', 'exists:hora,id'],
                'activa' => ['boolean'], // Opcional: si tienes un campo 'activa' en el formulario.
            ]);

            // Comprobar si ya existe una sesión para la misma sala, fecha y hora
            $existingSession = Sesion::where('fecha', $validatedData['fecha'])
                                     ->where('id_sala', $validatedData['sala_id'])
                                     ->where('hora', $validatedData['hora'])
                                     ->first();

            if ($existingSession) {
                return response()->json(['message' => 'Ya existe una sesión programada para esta sala, fecha y hora. Por favor, selecciona otra.'], 409);
            }

            $sesion = Sesion::create([
                'fecha' => $validatedData['fecha'],
                'id_sala' => $validatedData['sala_id'],
                'id_pelicula' => $validatedData['pelicula_id'],
                'hora' => $validatedData['hora'],
                'activa' => $validatedData['activa'] ?? true,
            ]);

            return response()->json(['message' => 'Sesión creada exitosamente.', 'sesion' => $sesion], 201);

        } catch (ValidationException $e) {
             Log::warning("Error de validación al añadir sesión: " . $e->getMessage());
             return response()->json([
                 'message' => 'Error de validación.',
                 'errors' => $e->errors()
             ], 422);
         } catch (\Exception $e) {
            Log::error("Error al crear la sesión: " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Error interno al crear la sesión.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene sesiones por fecha.
     */
    public function getSessionsByDate($fecha_id)
    {
        $fechaExiste = Fecha::where('id', $fecha_id)->exists();

        if (!$fechaExiste) {
            return response()->json(['message' => 'Fecha no encontrada.'], 404);
        }

        // Cargar las sesiones filtrando por la fecha_id con relaciones
        $sessions = Sesion::where('fecha', $fecha_id)
                            ->with(['pelicula', 'sala', 'fecha', 'horaSession']) // Asegúrate de 'horaSession'
                            ->get();

        $margenSeguridad = 10; // Minutos de limpieza o transición.

        // Formatear los datos para el frontend
        $formattedSessions = $sessions->map(function ($session) use ($margenSeguridad) {
            $hora_inicio_str = $session->horaSession->hora ?? null;
            $duracion_pelicula = $session->pelicula->duracion ?? 0;

            $hora_final_str = 'N/A';

            if ($hora_inicio_str) {
                try {
                    $hora_inicio_carbon = Carbon::parse("2000-01-01 {$hora_inicio_str}");
                    $hora_final_carbon = $hora_inicio_carbon
                                            ->addMinutes($duracion_pelicula)
                                            ->addMinutes($margenSeguridad);
                    $hora_final_str = $hora_final_carbon->format('H:i');

                } catch (\Exception $e) {
                    Log::error("Error al calcular hora final para sesión {$session->id}: " . $e->getMessage());
                    $hora_final_str = 'Error calculando';
                }
            }

            return [
                'id' => $session->id,
                'pelicula_titulo' => $session->pelicula->titulo ?? 'N/A',
                'sala_nombre' => $session->sala->nombre ?? ($session->sala->id_sala ?? 'N/A'), // Usar 'nombre' si existe, sino 'id_sala'
                'hora_inicio' => substr($hora_inicio_str, 0, 5) ?? 'N/A',
                'hora_final' => $hora_final_str,
                'fecha_sesion' => $session->fecha->fecha ?? 'N/A',
                'is_active' => $session->activa, // Asumo que la columna es 'activa'
            ];
        });

        return response()->json($formattedSessions);
    }

    /**
     * Elimina una sesión específica.
     */
    public function deleteSession($sesion_id)
    {
        try {
            $session = Sesion::find($sesion_id);

            if (!$session) {
                return response()->json(['message' => 'Sesión no encontrada.'], 404);
            }

            $session->delete();

            return response()->json(['message' => 'Sesión eliminada exitosamente.'], 200);

        } catch (\Exception $e) {
            Log::error("Error al eliminar la sesión con ID {$sesion_id}: " . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar la sesión.', 'error' => $e->getMessage()], 500);
        }
    }

}
