<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\PeticionPeliculasController; 
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Pelicula;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function mostrarLogin()
    {
        if (Auth::check()) {
            return redirect()->route('administrador.dashboard');
        }
        return view('administrador.loginAdministrador');
    }

    public function login(Request $request){

        // 1. Validar las credenciales de entrada. Laravel valida que los campos 'email', 'password' y 'codigo_administrador' estén presentes y tengan el formato básico.
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'codigo_administrador' => 'required|string',
        ]);


        // 2. Intentar encontrar un usuario en la base de datos que coincida con el Email Y el Código de Administrador proporcionados.
        $user = Administrator::where('email', $request->email)
                    ->where('codigo_administrador', $request->codigo_administrador)
                    ->first();

        // 3. Si se encontró un usuario con la combinación de email y código...
        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                // 4. Si las tres credenciales (email, código, Y contraseña verificada) son correctas:
                Auth::login($user);

                $request->session()->regenerate();

                // 5. Redirigir al usuario al dashboard de administración.
                return redirect()->route('administrador.dashboard');

            } else {
                return redirect()->back()->withErrors(['password' => 'Las credenciales proporcionadas no coinciden.'])->withInput($request->only('email', 'codigo_administrador'));
            }
        } else {
            return redirect()->back()->withErrors(['email' => 'Las credenciales proporcionadas no coinciden.'])->withInput($request->only('email', 'codigo_administrador'));
        }
    }

    public function searchTMDb(Request $request)
    {
        // 1. Obtener y validar todos los parámetros de filtro
        $request->validate([
            'query' => 'nullable|string|max:255',
            'list_type' => 'required|string|in:search,popular,upcoming,now_playing',
            'genre_id' => 'nullable|integer',
            'pages_to_fetch' => 'required|integer|min:1|max:5',
            'language' => 'required|string|in:es,en',
        ]);

        $searchTerm = $request->input('query');
        $listType = $request->input('list_type');
        $genreId = $request->input('genre_id');
        $pagesToFetch = (int) $request->input('pages_to_fetch');
        $searchYear = $request->input('year');
        $searchLanguage = $request->input('language');

        // Validar que haya un término de búsqueda si list_type es 'search'
        if ($listType === 'search' && empty($searchTerm)) {
            return response()->json(['error' => 'Debes introducir un título si buscas por nombre.'], 422);
        }

        // 2. Obtener la API Key
        $apiKey = env("API_KEY_TMDB");
        if (!$apiKey) {
            Log::error('API Key de TMDb no configurada.');
            return response()->json(['error' => 'API Key no configurada'], 500);
        }

        // 3. Determinar el endpoint base de la API y los parámetros iniciales según el list_type
        $apiUrl = '';
        $apiParams = [
            'api_key' => $apiKey,
            'language' => $searchLanguage,
        ];
        $allMovies = [];


        // --- Lógica para usar /search/movie o /discover/movie o /movie/{type} ---

        $useDiscover = false;

        switch ($listType) {
            case 'search':
                if (!empty($searchTerm)) {
                    $apiUrl = "https://api.themoviedb.org/3/search/movie";
                    $apiParams['query'] = $searchTerm;
                    if ($searchYear) {
                          $apiParams['year'] = $searchYear; // El filtro year funciona en /search/movie
                    }
                } else {
                     // Esto no debería pasar por la validación de arriba, pero como fallback
                    return response()->json(['error' => 'Título requerido para búsqueda por nombre.'], 422);
                }
                break;

            case 'popular':
            case 'upcoming':
            case 'now_playing':
                // Si hay filtros adicionales como género o año, es mejor usar /discover
                if ($genreId || $searchYear) {
                    $useDiscover = true;
                    $apiUrl = "https://api.themoviedb.org/3/discover/movie";
                } else {
                    $apiUrl = "https://api.themoviedb.org/3/movie/{$listType}";
                }
                break;

            default:
                 // Esto no debería pasar por la validación, pero como fallback
                $apiUrl = "https://api.themoviedb.org/3/search/movie";
                $apiParams['query'] = $searchTerm ?? '';
                Log::warning("Tipo de lista no reconocido: {$listType}. Usando búsqueda por defecto.");
                break;
        }

        // --- Si decidimos usar /discover, ajustamos los parámetros y la URL ---
        if ($useDiscover) {
            unset($apiParams['query']); // Discover no usa 'query' para búsqueda textual

            // Aplicamos ordenación para emular tipos de lista si usamos Discover
            switch ($listType) {
                case 'popular':
                    $apiParams['sort_by'] = 'popularity.desc';
                    break;
                case 'upcoming':
                     $apiParams['sort_by'] = 'primary_release_date.asc'; // Ordenar por fecha de estreno ascendente
                     $apiParams['primary_release_date.gte'] = now()->toDateString(); // Estreno igual o posterior a hoy
                    break;
                case 'now_playing': // O 'Estrenos'
                     $apiParams['sort_by'] = 'popularity.desc'; // O 'primary_release_date.desc'
                     $apiParams['primary_release_date.lte'] = now()->toDateString(); // Estreno igual o anterior a hoy
                    break;
            }

             // Aplicar filtro de género y año si están presentes y usamos Discover
            if ($genreId) {
                 $apiParams['with_genres'] = $genreId; // El filtro with_genres funciona en /discover
            }
            if ($searchYear) {
                 $apiParams['primary_release_year'] = $searchYear; // El filtro year funciona en /discover
            }
        }


        // 4. Realizar la llamada(s) HTTP a la API de TMDb para las páginas solicitadas
        for ($page = 1; $page <= $pagesToFetch; $page++) {
            $apiParams['page'] = $page;

            Log::info("Llamando a TMDb API (Página {$page}). URL: {$apiUrl}, Params: " . json_encode($apiParams)); // Log útil para depurar la llamada a la API

            $response = Http::get($apiUrl, $apiParams);

            if ($response->successful()) {
                $pageResults = $response->json()['results'] ?? [];
                $allMovies = array_merge($allMovies, $pageResults);

                $totalPagesAvailable = $response->json()['total_pages'] ?? $page;
                if ($page >= $totalPagesAvailable || count($pageResults) === 0) {
                break;
                }

            } else {
                // Si falla alguna página, registrar error
                $statusCode = $response->status();
                $errorMessage = $response->body();
                Log::error("Error al llamar a la API de TMDb (Página {$page}, Estatus {$statusCode}): URL: {$apiUrl}, Response: {$errorMessage}, Params: " . json_encode($apiParams));

            }
        }

        // 5. Devolver todos los resultados acumulados en formato JSON al frontend
        return response()->json($allMovies);
    }

    public function storeMovie(Request $request)
    {
        // 1. Validar el ID de TMDb recibido del frontend
        $request->validate([
            'tmdb_id' => 'required|integer|min:1', // El ID de TMDb debe ser un entero positivo
        ]);

        $tmdbId = $request->input('tmdb_id');

        // 2. Verificar si la película ya existe en nuestra base de datos (tabla 'peliculas')
        $existingMovie = Pelicula::where('id_api', $tmdbId)->first();

        if ($existingMovie) {
            return response()->json(['message' => 'Esta película ya ha sido añadida.', 'status' => 'duplicate'], 409); // 409 Conflict
        }

        // 3. Si no existe, llamar a la API de TMDb para obtener los detalles completos
        $apiKey = env("API_KEY_TMDB");
        if (!$apiKey) {
            return response()->json(['error' => 'API Key no configurada en el backend'], 500);
        }

        $apiUrlDetails = "https://api.themoviedb.org/3/movie/{$tmdbId}";
        $apiParamsDetails = [
            'api_key' => $apiKey,
            'language' => 'es',
        ];

        $responseDetails = Http::get($apiUrlDetails, $apiParamsDetails);

        if (!$responseDetails->successful()) {
            // Si falla la llamada para obtener detalles
            $statusCode = $responseDetails->status();
            $errorMessage = $responseDetails->body();
            Log::error("Error al obtener detalles de TMDb para ID {$tmdbId}: Estatus {$statusCode}, Mensaje: {$errorMessage}");
            return response()->json([
                'error' => 'No se pudieron obtener los detalles completos de la película de TMDb.',
                'details' => $errorMessage
            ], $statusCode >= 400 ? $statusCode : 500);
        }

        $movieDetails = $responseDetails->json();

        // 4. Validar y preparar los datos para guardar en la base de datos 'movies'
        try {
            $releaseDate = null;
            if (!empty($movieDetails['release_date'])) {
                try {
                    $releaseDate = Carbon::parse($movieDetails['release_date'])->toDateString();
                } catch (\Exception $e) {
                    Log::warning("No se pudo parsear la fecha de estreno '{$movieDetails['release_date']}' para TMDb ID {$tmdbId}. Error: {$e->getMessage()}");
                    $releaseDate = null;
                }
            }

            
            $movieToSave = new Pelicula();
            $movieToSave->id_api = $movieDetails['id'];
            $movieToSave->titulo = $movieDetails['title'] ?? $movieDetails['original_title'] ?? 'Título desconocido';
            $movieToSave->titulo_original = $movieDetails['original_title'] ?? null;
            $movieToSave->sinopsis = $movieDetails['overview'] ?? null;
            $movieToSave->fecha_estreno = $releaseDate;
            $movieToSave->poster_ruta = $movieDetails['poster_path'] ?? null;
            $movieToSave->backdrop_ruta = $movieDetails['backdrop_path'] ?? null;
            $movieToSave->video = $movieDetails['video'] ?? false; // El campo video es booleano
            $movieToSave->adult = $movieDetails['adult'] ?? false;   // El campo adult es booleano

            $movieToSave->duracion = $movieDetails['runtime'] ?? null;
            $movieToSave->puntuacion_promedio = $movieDetails['vote_average'] ?? 0;
            $movieToSave->numero_votos = $movieDetails['vote_count'] ?? 0;
            $movieToSave->popularidad = $movieDetails['popularity'] ?? 0;

            $movieToSave->id_sala = $request->input('id_sala', 1);
            $movieToSave->activa = $request->input('activa', false);

            $spokenLanguages = $movieDetails['spoken_languages'] ?? [];
            $spokenLanguageCodes = collect($spokenLanguages)->pluck('iso_639_1')->toArray(); // Extrae solo los códigos ISO (ej: ["en", "es"])
            $movieToSave->lenguaje_original = json_encode($spokenLanguageCodes);

            $genreIds = collect($movieDetails['genres'] ?? [])->pluck('id')->toArray();
            /* $movieToSave->genre_ids = json_encode($genreIds); // Guardar el array de IDs como JSON string */

            // 5. Guardar la película en la base de datos
            $movieToSave->save();

            // ****** Manejo de Géneros (Lógica post-guardado si usas la relación Many-to-Many) ******
            // Si quieres guardar los géneros de la API en tu tabla 'genero_pelicula'
            // y vincularlos a la película guardada en la tabla pivote 'pelicula_genero',
            // la lógica iría AQUÍ, DESPUÉS de $movieToSave->save();
            // $apiGenreObjects = $movieDetails['genres'] ?? []; // Obtener los objetos de género de la API
            // $localGenreIds = []; // Array para almacenar los IDs de tus géneros locales
            // foreach ($apiGenreObjects as $apiGenre) {
            //     Buscar o crear el género en tu tabla 'genero_pelicula' por su id_api
            //     $localGenre = \App\Models\GeneroPelicula::firstOrCreate(
            //         ['id_api' => $apiGenre['id']],
            //         ['nombre' => $apiGenre['name'] ?? 'Nombre Desconocido'] // Usar el nombre de la API si está disponible
            //     );
            //     $localGenreIds[] = $localGenre->id; // Asumimos que tu tabla genero_pelicula tiene una PK 'id'
            // }
            // Asegúrate de que tu modelo Pelicula tiene una relación many-to-many llamada 'generos'
            // public function generos() { return $this->belongsToMany(\App\Models\GeneroPelicula::class, 'pelicula_genero', 'pelicula_id', 'genero_id'); }
            // $movieToSave->generos()->sync($localGenreIds); // Sincronizar la relación en la tabla pivote

            // 6. Devolver una respuesta de éxito al frontend
            return response()->json([
                'message' => 'Película añadida con éxito.',
                'status' => 'success',
                'movie' => $movieToSave
            ], 201);

        } catch (\Exception $e) {
            // Capturar cualquier error durante el procesamiento de datos o guardado
            Log::error("Error al procesar y guardar película con TMDb ID {$tmdbId}: " . $e->getMessage());
            // Si estás en debug, puedes querer mostrar $e->getMessage()
            return response()->json(['error' => 'Ocurrió un error al guardar la película.', 'details' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        $generos_tmdb = PeticionPeliculasController::peticion_generos();
        return view('administrador.dashboard', compact('generos_tmdb'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/administrador')->with('success', '¡Sesión de administrador cerrada correctamente!');
    }
}
