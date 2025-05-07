<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Cosmos Cinema</title>
    @vite(['resources/css/dashboard.css'])

    {{-- Aquí podrías enlazar archivos JavaScript específicos del dashboard más adelante --}}
    {{-- @vite(['resources/js/adminDashboard.js']) --}}
</head>
<body>
    {{-- Contenedor principal del Dashboard: usaremos Flexbox o Grid en CSS para la distribución --}}
    <div class="dashboard-layout">

        {{-- Barra Superior (Header) --}}
        <header class="dashboard-header">
            {{-- Contenedor para los elementos de la derecha (Nombre del admin y Logo) --}}
            <div class="header-right-elements">
                {{-- Nombre del Administrador --}}
                {{-- Auth::user() accede al modelo del admin logueado (tu modelo Administrator) --}}
                {{-- Muestra el nombre_user_admin, o el nombre, o el email si los anteriores no existen --}}
                <span class="admin-name">{{ Auth::user()->nombre_user_admin ?? Auth::user()->nombre ?? Auth::user()->email ?? 'Admin' }}</span>

                {{-- Logo de la Empresa --}}
                {{-- Reemplaza con la ruta real de tu logo --}}
                <div class="company-logo">
                    {{-- Ajusta la ruta asset() a la ubicación de tu logo --}}
                    <img src="{{ asset('images/logoCosmosCinema.png') }}" alt="Logo Empresa Cosmos Cinema">
                </div>
            </div>
        </header>

        <div class="main-dashboard-content">
        <aside class="dashboard-sidebar">
            <nav class="sidebar-nav">

                <div class="sidebar-menu">
                    <ul>
                        <li>
                            <a href="#" class="sidebar-link active" data-section="add-movies">Añadir peliculas</a>
                        </li>
                        <li>
                            <a href="#" class="sidebar-link" data-section="manage-movies">Gestionar películas</a>
                        </li>
                    </ul>
                </div>
                <div class="sidebar-bottom">
                    <form action="{{ route('administrador.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="sidebar-logout-btn">Cerrar Sesión</button>
                    </form>
                </div>
            </nav>
        </aside>

            <main class="dashboard-content-area">
                <section id="add-movies-section" class="content-section">
                    <h3>Añadir peliculas</h3>

                    <div class="api-search-filters">
                        <input type="text" id="api-search-input" placeholder="Buscar película por título en TMDb">

                        <input type="number" id="api-year-input" placeholder="Año de lanzamiento (Opcional)">

                        
                        <select id="api-genre-select">
                            <option value="">Todos los géneros</option>
                            @foreach ($generos_tmdb ?? [] as $genero)
                                <option value="{{ $genero['id'] }}">{{ $genero['name'] }}</option>
                            @endforeach
                        </select>

                        <input type="number" id="api-page-input" value="1" min="1" placeholder="Página">

                        <select id="api-language-select">
                            <option value="es">Español</option>
                            <option value="en">Inglés</option>
                        </select>
                        

                        <button id="api-search-button">Buscar</button>
                    </div>

                    <hr>

                    <div class="api-results-area">
                        <p>Introduce un título y haz clic en "Buscar" para encontrar películas en TMDb.</p>
                        <div class="api-movie-item">
                            <img src="URL_DEL_POSTER" alt="Poster de la película">
                            <div class="movie-details">
                                <h4>Nombre de la Película (Año)</h4>
                                <p>Sinopsis corta...</p>
                            </div>
                            <button class="add-movie-btn" data-tmdb-id="ID_DE_TMDB_AQUI">Añadir pelicula</button>
                        </div>
                    </div>

                </section>
                <section id="manage-movies-section" class="content-section hidden">
                    <h3>Gestionar películas en BD</h3>
                    <p>Contenido para listar y gestionar películas de nuestra base de datos...</p>
                </section>
            </main>
        </div>
    </div>
</body>
</html>