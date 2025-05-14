<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\PeticionPeliculasController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Rules\letraDNI;
use App\Models\Pelicula;
use App\Models\User;
use App\Models\Ciudad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\MenuItem;

//Función para mostrar el login de administrador
class AdminController extends Controller
{
    public function mostrarLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('administrador.dashboard');
        }
        return view('administrador.loginAdministrador');
    }

    //Login del administradores, validación de campos
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'codigo_administrador' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // --- PASO 1: Intentar el login estándar de usuario (tabla 'users') ---
        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();

            // --- PASO 2: Ahora que el usuario está autenticado en 'users', verificar si es administrador ---
            $administrator = Administrator::where('email', $user->email)
                ->where('codigo_administrador', $request->codigo_administrador)
                ->first();

            // --- PASO 3: Si se encuentra el registro en 'administrador' con el código correcto, es administrador ---
            if ($administrator) {
                Auth::guard('web')->logout();
                Auth::guard('admin')->login($administrator);
                $request->session()->regenerate();

                // Redirigir al administrador
                return redirect()->route('administrador.dashboard');
            } else {
                // --- NO ES ADMINISTRADOR ---
                Auth::guard('web')->logout();
                return back()->withErrors(['codigo_administrador' => 'Las credenciales de usuario proporcionadas no coinciden.'])->withInput($request->only('email', 'codigo_administrador'));
            }
        } else {
            return back()->withErrors([
                'email' => 'Las credenciales de usuario proporcionadas no coinciden.',
            ])->onlyInput('email');
        }
    }



    //Comparar peliculas de la base de datos con las que nos muestra la api para saber cual tenemos ya en la base de datos.
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

            //Guardar la película de la API
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
            $movieToSave->estreno = $request->input('estreno', true);

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
        /* $genreId = $request->input('genre_id'); */
        $status = $request->input('status', 'all');
        $itemsPerPage = $request->input('items_per_page', 10);
        $itemsPerPage = (int) $itemsPerPage; // Asegurarse de que es un entero

        // Validar itemsPerPage para evitar valores excesivos o inválidos
        $itemsPerPage = max(1, min(50, $itemsPerPage)); // Limitar entre 1 y 50

        // 2. Construir la consulta Eloquent
        $movies = Pelicula::query(); // Iniciar una nueva consulta sobre el modelo Pelicula

        // Aplicar filtro por título si se proporciona un término de búsqueda
        if (!empty($query)) {
            $movies->where(function ($q) use ($query) {
                // Buscar en el título principal O en el título original
                $q->where('titulo', 'like', '%' . $query . '%')
                    ->orWhere('titulo_original', 'like', '%' . $query . '%');
            });
        }


        // Aplicar filtro por género si se proporciona un ID de género, cuando lo solucionemos
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

    //Estrenos
    public function EstrenoStatus($id)
    {
        $movie = Pelicula::findOrFail($id);

        $movie->estreno = !$movie->estreno;

        $movie->save();

        $newStatusText = $movie->estreno ? 'Estreno' : 'Cartelera';

        // Devolver una respuesta de éxito al frontend
        return response()->json([
            'message' => "Estado de cartelera/estreno actualizado a '{$newStatusText}'.",
            'new_status' => $movie->estreno,
            'new_status_text' => $newStatusText
        ]);
    }

    public function obtenerMenu(Request $request)
    {
        try {
            $query = MenuItem::orderBy('nombre');

            // Filtrar por búsqueda
            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                $query->where('nombre', 'like', '%' . $searchTerm . '%')
                    ->orWhere('descripcion', 'like', '%' . $searchTerm . '%');
            }

            // Filtrar por estado (activo/inactivo)
            if ($request->has('status') && $request->input('status') !== 'all') {
                $status = $request->input('status');
                $query->where('activo', $status); // Filtra directamente usando el valor 1 o 0
            }

            // Paginación
            $perPage = $request->input('perPage', 10); // Default a 10 elementos por página
            $menuItems = $query->paginate($perPage);

            return response()->json($menuItems);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al cargar los elementos del menú.'], 500);
        }
    }

    public function estadoActivo($id)
    {

        try {
            //Validamos si el elemento del menu existe
            $menuItem = MenuItem::findOrFail($id);

            //Cambiamos el estado a activo
            $menuItem->activo = !$menuItem->activo;

            //Guaradmos los cambios en la BBDD
            $menuItem->save();

            //Respuesta Json
            $newStatusText = $menuItem->activo ? 'Activado' : 'Desactivado';

            return response()->json([
                'message' => "Estado de elemento del menú actualizado a '{$newStatusText}'.",
                'new_status' => $menuItem->activo,
                'new_status_text' => $newStatusText,
                'item_id' => $menuItem->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al actualizar el estado del elemento del menú.'], 500);
        }
    }

    //Función para añadir un nuevo producto en la BBDD
    public function añadirProducto(Request $request)
    {

        //Validar los datos recibidos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'foto' => 'nullable|image|max:2048',
        ]);

        try {
            //Nuevo producto
            $newItem = new MenuItem();

            //Asignamos los input, con las columnas de la base de datos
            $newItem->nombre = $request->input('nombre');
            $newItem->descripcion = $request->input('descripcion');
            $newItem->precio = $request->input('precio');

            //Manejar la subida de la foto
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $fileName = time() . '_' . $file->getClientOriginalName();
                // Guardar directamente en la carpeta public/images/menus
                $file->move(public_path('images/menus'), $fileName);
                $newItem->imagen_url = '/images/menus/' . $fileName;
            } else {
                $newItem->imagen_url = "/images/menus/imagenDefecto.jpeg";
            }

            //Guardamos en la BBDD
            $newItem->save();

            //Respuesta
            return response()->json([
                'message' => 'Elemento del menú añadido con éxito.',
                'item' => $newItem
            ], 201);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Ocurrió un error al añadir el elemento del menú.'], 500);
        }
    }

    //Función para obtener un producto en concreto para editar
    public function obtenerProducto($id)
    {
        try {

            //Obtener el menu por su id
            $menuItem = MenuItem::findOrFail($id);
            return response()->json($menuItem);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los detalles del elemento del menú.'], 500);
        }
    }

    public function actualizarProducto(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
        ]);

        try {
            $menuItem = MenuItem::findOrFail($id);
            $menuItem->nombre = $request->input('nombre');
            $menuItem->descripcion = $request->input('descripcion');
            $menuItem->precio = $request->input('precio');

            $menuItem->save();

            return response()->json([
                'message' => 'Elemento del menú actualizado con éxito.',
                'item' => $menuItem
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al actualizar el elemento del menú.'], 500);
        }
    }

    //Crear nuevo Empleado (Desde Administrador)
    public function crearEmpleado(Request $request)
    {

        $mensajes = [
            'nombre.required' => 'El campo Nombre es obligatorio.',
            'nombre.string' => 'El campo Nombre debe ser una cadena de texto.',
            'nombre.max' => 'El campo Nombre no debe exceder los :max caracteres.',

            'apellidos.required' => 'El campo Apellidos es obligatorio.',
            'apellidos.string' => 'El campo Apellidos debe ser una cadena de texto.',
            'apellidos.max' => 'El campo Apellidos no debe exceder los :max caracteres.',

            'direccion.required' => 'El campo Dirección es obligatorio.',
            'direccion.string' => 'El campo Dirección debe ser una cadena de texto.',
            'direccion.max' => 'El campo Dirección no debe exceder los :max caracteres.',
            'direccion.regex' => 'El campo Dirección contiene caracteres no permitidos.',

            'ciudad.required' => 'Debes seleccionar una ciudad.',
            'ciudad.exists' => 'La ciudad seleccionada no es válida.',

            'codigo_postal.required' => 'El campo Código Postal es obligatorio.',
            'codigo_postal.string' => 'El campo Código Postal debe ser una cadena de texto.',

            'codigo_postal.max' => 'El Código Postal debe tener exactamente :max dígitos.',
            'codigo_postal.min' => 'El Código Postal debe tener exactamente :min dígitos.',

            'numero_telefono.required' => 'El campo Número de Teléfono es obligatorio.',
            'numero_telefono.digits' => 'El campo Número de Teléfono debe tener exactamente :digits dígitos.',

            'dni.required' => 'El campo DNI es obligatorio.',
            'dni.regex' => 'El formato del DNI debe ser 8 números seguidos de una letra.',
            'dni.unique' => 'Ya existe un usuario con este DNI registrado.',

            'fecha_nacimiento.required' => 'El campo Fecha de Nacimiento es obligatorio.',
            'fecha_nacimiento.date' => 'El campo Fecha de Nacimiento debe ser una fecha válida.',
            'fecha_nacimiento.before' => 'El campo Fecha de Nacimiento debe ser una fecha anterior a :date.',

            'tipo_usuario.required' => 'Debes seleccionar el tipo de usuario.',
            'tipo_usuario.integer' => 'El campo Tipo de Usuario debe ser un número entero.',
            'tipo_usuario.in' => 'El tipo de usuario seleccionado no es válido.',
        ];
        // Validación de los datos del formulario con reglas combinadas/adaptadas
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:100'], // Ajustado max, quitado alpha
            'apellidos' => ['required', 'string', 'max:100'], // Ajustado max, quitado alpha
            'direccion' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\/\-#.,]*$/'],
            'ciudad' => ['required', 'exists:ciudades,id'],
            'codigo_postal' => ['required', 'string', 'max:5', 'min:5'],
            'numero_telefono' => ['required', 'digits:9'], // <<-- Usamos 'digits:9' y el nombre 'numero_telefono'
            'dni' => ['required','regex:/^\d{8}[A-Za-z]$/','unique:users,dni',new letraDNI],
            'fecha_nacimiento' => ['required', 'date', 'before:today'], // De ambos, añadido before:today
            'tipo_usuario' => ['required', 'integer', 'in:1,2'], // De AdminController
        ], $mensajes);

        // Si falla la validación...
        if ($validator->fails()) {
            $ciudades = Ciudad::orderBy('nombre')->get();
            return back()->withErrors($validator)->withInput()->with(compact('ciudades'));
        }

        $validatedData = $validator->validated();

        // --- Lógica de Creación de Usuario (Email, Password, Mayor_Edad) ---

        // Lógica para normalizar partes del nombre/apellido/dni para el email
        $normalizeEmailPart = function ($string) {
            $string = Str::lower($string);
            $string = str_replace(' ', '', $string);
            $string = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü'], ['a', 'e', 'i', 'o', 'u', 'n', 'u', 'A', 'E', 'I', 'O', 'U', 'N', 'U'], $string);
            $string = preg_replace('/[^a-z0-9]/', '', $string);
            return $string;
        };

        $nombreNormalizado = $normalizeEmailPart($validatedData['nombre']);
        $apellidosNormalizado = $normalizeEmailPart($validatedData['apellidos']);
        $letraDni = Str::upper(substr($validatedData['dni'], -1)); // Última letra del DNI

        // Generar email
        // Sigamos el original: nombre.apellido.letraDNI@cosmosAdmin.com
        $generatedEmail = $nombreNormalizado . '.' . $apellidosNormalizado . '.' . $letraDni . '@cosmosAdmin.com'; // <<-- ¡VERIFICA Y AJUSTA TU DOMINIO!

        //Si existe se le añade un número
        $originalEmail = $generatedEmail;
        $counter = 1;
        while (User::where('email', $generatedEmail)->exists()) {
            $generatedEmail = $originalEmail . $counter;
            $counter++;
        }


        $defaultPassword = ''; // Inicializa la variable

        if ($validatedData['tipo_usuario'] == 1) { // Si el tipo de usuario seleccionado es Administrador
            $defaultPassword = 'CosmosAdmin123';
        } else if ($validatedData['tipo_usuario'] == 2) { // Si el tipo de usuario seleccionado es Empleado
            $defaultPassword = 'CosmosEmpleado123';
        }

        $hashedPassword = Hash::make($defaultPassword);

        // Calcular si es mayor de edad
        $fechaNacimiento = new \DateTime($validatedData['fecha_nacimiento']);
        $hoy = new \DateTime();
        $edad = $hoy->diff($fechaNacimiento)->y;
        $isMayorEdad = $edad >= 18;


        // Crear el usuario en la tabla 'users'
        $user = User::create([
            'nombre' => $validatedData['nombre'],
            'apellidos' => $validatedData['apellidos'],
            'email' => $generatedEmail, // Usar el email generado
            'password' => $hashedPassword, // Usar la password generada y hasheada
            'fecha_nacimiento' => $validatedData['fecha_nacimiento'],
            'numero_telefono' => $validatedData['numero_telefono'],
            'dni' => $validatedData['dni'],
            'direccion' => $validatedData['direccion'],
            'ciudad' => $validatedData['ciudad'], // Guardar el ID de la ciudad
            'codigo_postal' => $validatedData['codigo_postal'],
            'mayor_edad' => $isMayorEdad, // Usar el valor calculado
            'id_descuento' => 1, // Por defecto 1 (ajusta si necesitas lógica para esto)
            'tipo_usuario' => $validatedData['tipo_usuario'],
        ]);

        // Si el tipo de usuario Administrador, crear registro en tabla 'administrador'
        if ($user->tipo_usuario == 1) {

            $codigoAdmin = '';
            $isUniqueCode = false;

            do {
                // Generar una letra aleatoria
                $letra1 = Str::random(1, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');

                // Generar dos números aleatorios
                $numeros = sprintf('%02d', rand(0, 99));

                // Generar una segunda letra aleatoria
                $letra2 = Str::random(1, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');

                $codigoAdmin = $letra1 . $numeros . $letra2;

                // Verificar si este código ya existe en la tabla 'administrador'
                $isUniqueCode = !Administrator::where('codigo_administrador', $codigoAdmin)->exists();

            } while (!$isUniqueCode); // Repetir el proceso si el código generado ya existe

            // Generar Nombre de Usuario Administrador único
            $nombreUserAdmin = '';
            $isUniqueAdminName = false;

            // Crear una base para el nombre de usuario a partir del nombre validado
            $nombreBaseAdmin = Str::slug($validatedData['nombre'], '_');
            do {

                // Generar un sufijo numérico aleatorio
                $randomSuffix = rand(100, 999);

                // Combinar para formar el nombre de usuario
                $nombreUserAdmin = $nombreBaseAdmin . '_Cosmos' . $randomSuffix;

                // Verificar si este nombre de usuario ya existe en la tabla 'administrador'
                $isUniqueAdminName = !Administrator::where('nombre_user_admin', Str::ucfirst($nombreUserAdmin))->exists();

            } while (!$isUniqueAdminName); // Repetir si el nombre de usuario generado ya existe

            // Crear el registro en la tabla 'administrador'
            // Asegúrate de usar el email generado, no el validado
            Administrator::create([
                'email' => $user->email, // Usar el email del objeto $user creado
                'codigo_administrador' => Str::upper($codigoAdmin),
                'nombre_user_admin' => Str::ucfirst($nombreUserAdmin),
            ]);
        }
        
            return response()->json([
                'message' => 'Empleado creado exitosamente.',
            ], 201);

    }

    //Función index que returnea la vista, con los géneros para el select
    public function index()
    {
        $generos_tmdb = PeticionPeliculasController::peticion_generos();
        $ciudades = Ciudad::orderBy('nombre')->get();
        return view('administrador.dashboard', compact('generos_tmdb','ciudades'));
    }

    //Función logout, que nos rederige al login de administrador
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/administrador')->with('success', '¡Sesión de administrador cerrada correctamente!');
    }
}
