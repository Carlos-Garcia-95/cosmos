<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Cosmos Cinema</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/dashboard.css'])
    @vite(['resources/js/adminDashboard.js'])
    @vite(['resources/js/adminDashboardGestionarPelicula.js'])
</head>
<body>
    <div class="dashboard-layout">
        <header class="dashboard-header">
            <div class="header-right-elements">
                <span class="admin-name">{{ Auth::user()->nombre_user_admin ?? Auth::user()->nombre ?? Auth::user()->email ?? 'Admin' }}</span>
                <div class="company-logo">
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

                        <input type="text" id="api-search-input" placeholder="Buscar por título">

                        <select id="api-list-type-select">
                            <option value="search">Buscar por Título</option>
                            <option value="popular">Populares</option>
                            <option value="upcoming">Próximas</option>
                            <option value="now_playing">En cines</option>
                        </select>

                        <select id="api-genre-select">
                            <option value="">Todos los géneros</option>
                            @foreach ($generos_tmdb ?? [] as $genero)
                                <option value="{{ $genero['id'] }}">{{ $genero['name'] }}</option>
                            @endforeach
                        </select>

                        <select id="api-quantity-select">
                            <option value="1">20 películas (1 página)</option>
                            <option value="2">40 películas (2 páginas)</option>
                            <option value="3">60 películas (3 páginas)</option>
                            <option value="4">80 películas (4 páginas)</option>
                            
                        </select>

                        <select id="api-language-select">
                            <option value="es">Español</option>
                            <option value="en">Inglés</option>
                        </select>
                        {{-- <input type="number" id="api-page-input" value="1" min="1" placeholder="Página"> --}}


                        
                        <button id="api-search-button">Buscar</button>
                    </div>

                    <hr>

                    <div class="api-results-area">
                        <p>Haz clic en "Buscar" para encontrar películas en TMDb.</p>
                        
                    </div>

                    <div class="api-pagination-controls" style="text-align: center; margin-top: 20px;">
                        <button id="prev-page-btn" disabled>Anterior</button>
                        <span id="page-info">Página 1 de 1</span>
                        <button id="next-page-btn" disabled>Siguiente</button>
                    </div>

                </section>
                <section id="manage-movies-section" class="content-section hidden">
                    <h3>Gestionar películas en BD</h3>

                    <div class="manage-filters">

                        <input type="text" id="manage-search-input" placeholder="Buscar por título">

                        <select id="manage-genre-select">
                            <option value="">Todos los géneros</option>
                            @foreach ($generos_tmdb ?? [] as $genero)
                                <option value="{{ $genero['id'] }}">{{ $genero['name'] }}</option>
                            @endforeach
                        </select>

                        <select id="manage-status-select">
                            <option value="all">Todos los estados</option>
                            <option value="active">Activas</option>
                            <option value="inactive">Inactivas</option>
                        </select>

                        <select id="manage-items-per-page-select">
                            <option value="5">5 por página</option>
                            <option value="10">10 por página</option>
                            <option value="15">15 por página</option>
                            <option value="20">20 por página</option>
                        </select>

                        <button id="manage-filter-button">Aplicar Filtros</button>
                    </div>

                    <div class="manage-movies-area">
                        <p>Selecciona filtros y haz clic en "Aplicar Filtros" para cargar la lista.</p>
                    </div>

                    <div class="manage-pagination-controls">
                        <button id="manage-prev-page-btn" disabled>Anterior</button>
                        <span id="manage-page-info">Página 0 de 0 (0 películas en total)</span>
                        <button id="manage-next-page-btn" disabled>Siguiente</button>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>
</html>