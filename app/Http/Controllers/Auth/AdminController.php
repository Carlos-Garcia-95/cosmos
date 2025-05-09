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
        if (Auth::guard('admin')->check()) {
            return redirect()->route('administrador.dashboard');
        }
        return view('administrador.loginAdministrador');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'codigo_administrador' => 'required|string',
        ]);

        $user = Administrator::where('email', $request->email)
            ->where('codigo_administrador', $request->codigo_administrador)
            ->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                Auth::guard('admin')->login($user);
                $request->session()->regenerate();
                return redirect()->route('administrador.dashboard');
            } else {
                return redirect()->back()->withErrors(['password' => 'Las credenciales proporcionadas no coinciden.'])->withInput($request->only('email', 'codigo_administrador'));
            }
        } else {
            return redirect()->back()->withErrors(['email' => 'Las credenciales proporcionadas no coinciden.'])->withInput($request->only('email', 'codigo_administrador'));
        }
    }

    //Conparar peliculas de la base de datos con las que nos muestra la api para saber cual tenemos ya en la base de datos.
    public function searchTMDb(Request $request)
    {
        $query = $request->input('query');
        $listType = $request->input('list_type', 'search');
        $genreId = $request->input('genre_id');
        $pagesToFetch = $request->input('pages_to_fetch', 1);
        $searchLanguage = $request->input('language', 'en');

        $apiKey = env("API_KEY_TMDB");

        if (!$apiKey) {
            return response()->json(['error' => 'API Key no configurada en el backend'], 500);
        }

        $allMovies = [];

        for ($page = 1; $page <= $pagesToFetch; $page++) {
            $apiUrl = '';
            $apiParams = [
                'api_key' => $apiKey,
                'language' => $searchLanguage,
                'page' => $page,
            ];

            if ($listType === 'search') {
                if (empty($query)) {
                    continue;
                }
                $apiUrl = "https://api.themoviedb.org/3/search/movie";
                $apiParams['query'] = $query;
            } elseif ($listType === 'popular') {
                $apiUrl = "https://api.themoviedb.org/3/movie/popular";
                if (!empty($genreId)) {
                    $apiUrl = "https://api.themoviedb.org/3/discover/movie";
                    $apiParams['with_genres'] = $genreId;
                }
            } elseif ($listType === 'now_playing') {
                $apiUrl = "https://api.themoviedb.org/3/movie/now_playing";
                if (!empty($genreId)) {
                    $apiUrl = "https://api.themoviedb.org/3/discover/movie";
                    $apiParams['with_genres'] = $genreId;
                }
            } elseif ($listType === 'upcoming') {
                $apiUrl = "https://api.themoviedb.org/3/movie/upcoming";
                if (!empty($genreId)) {
                    $apiUrl = "https://api.themoviedb.org/3/discover/movie";
                    $apiParams['with_genres'] = $genreId;
                    $apiParams['primary_release_date.gte'] = now()->toDateString();
                    $apiParams['sort_by'] = 'primary_release_date.asc';
                }
            } else {
                if ($page === 1) {
                    return response()->json(['error' => 'Tipo de lista no soportado.'], 400);
                }
                continue;
            }

            if (empty($apiUrl)) {
                continue;
            }

            $response = Http::get($apiUrl, $apiParams);

            if ($response->successful()) {
                $data = $response->json();
                $allMovies = array_merge($allMovies, $data['results'] ?? []);

                if (!isset($data['total_pages']) || !is_numeric($data['total_pages']) || ($data['total_pages'] <= $page)) {
                    break;
                }
            } else {
                $statusCode = $response->status();
                $errorMessage = $response->body();
                Log::error("Error al llamar a TMDb API (Página {$page}): Estatus {$statusCode}, Mensaje: {$errorMessage}");
                break;
            }
        }

        $tmdbIds = collect($allMovies)->pluck('id')->filter()->unique()->toArray();

        if (empty($tmdbIds)) {
            return response()->json([]);
        }

        $existingMovieIds = Pelicula::whereIn('id_api', $tmdbIds)
            ->pluck('id_api')
            ->toArray();

        $existingMovieIdsMap = array_fill_keys($existingMovieIds, true);

        $moviesWithStatus = collect($allMovies)->map(function ($movie) use ($existingMovieIdsMap) {
            $movie['is_added'] = array_key_exists($movie['id'], $existingMovieIdsMap);
            return $movie;
        })->values()->all();

        return response()->json($moviesWithStatus);
    }

    //Obtener peliculas de la API y guardarla en la base de datos
    public function storeMovie(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer|min:1',
        ]);

        $tmdbId = $request->input('tmdb_id');

        $existingMovie = Pelicula::where('id_api', $tmdbId)->first();

        if ($existingMovie) {
            return response()->json(['message' => 'Esta película ya ha sido añadida.', 'status' => 'duplicate'], 409);
        }

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
            $statusCode = $responseDetails->status();
            $errorMessage = $responseDetails->body();
            return response()->json([
                'error' => 'No se pudieron obtener los detalles completos de la película de TMDb.',
                'details' => $errorMessage
            ], $statusCode >= 400 ? $statusCode : 500);
        }

        $movieDetails = $responseDetails->json();

        try {
            $releaseDate = null;
            if (!empty($movieDetails['release_date'])) {
                try {
                    $releaseDate = Carbon::parse($movieDetails['release_date'])->toDateString();
                } catch (\Exception $e) {
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
            $movieToSave->adult = $movieDetails['adult'] ?? false;
            $movieToSave->video = $movieDetails['video'] ?? false;

            $movieToSave->duracion = $movieDetails['runtime'] ?? null;
            $movieToSave->puntuacion_promedio = $movieDetails['vote_average'] ?? 0;
            $movieToSave->numero_votos = $movieDetails['vote_count'] ?? 0;
            $movieToSave->popularidad = $movieDetails['popularity'] ?? 0;

            $spokenLanguages = $movieDetails['spoken_languages'] ?? [];
            $spokenLanguageCodes = collect($spokenLanguages)->pluck('iso_639_1')->toArray();
            $movieToSave->lenguaje_original = json_encode($spokenLanguageCodes);

            $movieToSave->id_sala = $request->input('id_sala', 1);
            $movieToSave->activa = $request->input('activa', false);

            /* $genreIds = collect($movieDetails['genres'] ?? [])->pluck('id')->toArray();
            $movieToSave->genre_ids = json_encode($genreIds); */

            $movieToSave->save();

            // Logic for Many-to-Many Genres relationship would go here after save()

            return response()->json([
                'message' => 'Película añadida con éxito.',
                'status' => 'success',
                'movie' => $movieToSave
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al guardar la película.', 'details' => $e->getMessage()], 500);
        }
    }

    //Obtener peliculas de la base de datos
    public function obtenerPeliculas(Request $request)
    {
        // 1. Obtener los parámetros de filtro y paginación de la request
        $query = $request->input('query');
        $genreId = $request->input('genre_id');
        $status = $request->input('status', 'all');
        $itemsPerPage = $request->input('items_per_page', 10);
        $itemsPerPage = (int) $itemsPerPage; // Asegurarse de que es un entero

        // Validar itemsPerPage para evitar valores excesivos o inválidos
        $itemsPerPage = max(1, min(50, $itemsPerPage)); // Limitar entre 1 y 50

        // 2. Construir la consulta Eloquent
        $movies = Pelicula::query(); // Iniciar una nueva consulta sobre el modelo Pelicula

        // Aplicar filtro por título si se proporciona un término de búsqueda
        if (!empty($query)) {
            $movies->where(function($q) use ($query) {
                // Buscar en el título principal O en el título original
                $q->where('titulo', 'like', '%' . $query . '%')
                ->orWhere('titulo_original', 'like', '%' . $query . '%');
            });
        }

        // Aplicar filtro por género si se proporciona un ID de género
        /* if (!empty($genreId)) {
            $movies->whereHas('generos', function ($q) use ($genreId) {
                $q->where('genero_pelicula.id', $genreId);
            });
        } */


        // Aplicar filtro por estado 'activa'
        if ($status === 'active') {
            $movies->where('activa', true);
        } elseif ($status === 'inactive') {
            $movies->where('activa', false);
        }

        // 3. Aplicar paginación
        $paginatedMovies = $movies->paginate($itemsPerPage);

        // 4. Devolver los resultados paginados como JSON
        return response()->json($paginatedMovies);
    }

    public function estadoPelicula(Request $request, $id)
    {
        // 1. Validar que la película con el ID proporcionado existe
        $movie = Pelicula::findOrFail($id);

        // 2. Cambiar el estado 'activa'
        $movie->activa = !$movie->activa;

        // 3. Guardar el cambio en la base de datos
        $movie->save();

        // 4. Devolver una respuesta de éxito con el nuevo estado
        return response()->json([
            'message' => 'Estado de película actualizado con éxito.',
            'new_status' => $movie->activa,
            'movie_id' => $movie->id
        ]);
    }


    //Función index que returnea la vista, con los géneros para el select
    public function index()
    {
        $generos_tmdb = PeticionPeliculasController::peticion_generos();
        return view('administrador.dashboard', compact('generos_tmdb'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/administrador')->with('success', '¡Sesión de administrador cerrada correctamente!');
    }
}
