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
    @vite(['resources/js/adminDashboardGestionarMenu.js'])
    @vite(['resources/js/adminDashboardSesiones.js'])
</head>

<body>
    <div class="dashboard-layout">
        <header class="dashboard-header">
        <!-- <button class="menu-toggle" aria-label="Abrir menú">☰</button> -->
            <div class="header-right-elements">
                <span class="admin-name">
                    @php
                        $adminName = '';
                        if (Auth::check()) {
                            $user = Auth::user();
                            $fullName = trim(($user->nombre ?? '') . ' ' . ($user->apellido ?? ''));
                            $adminName = $fullName ?: ($user->nombre_user_admin ?? $user->nombre ?? $user->email ?? 'Admin');
                        } else {
                            $adminName = 'Admin'; // O lo que quieras mostrar si no hay usuario logueado
                        }
                    @endphp
                    {{ $adminName }}
                </span>
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
                            <li>
                                <a href="#" class="sidebar-link" data-section="manage-menu">Gestionar Menú Cosmos</a>
                            </li>
                            <li>
                                <a href="#" class="sidebar-link" data-section="create-session">Gestionar Sesión</a>
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

                <section id="manage-menu-section" class="content-section hidden">
                    <h3>Gestionar Menú Cosmos</h3>

                    <div>
                        <button id="add-new-menu-item-button">Añadir Nuevo Elemento al Menú</button>
                    </div>

                    <div class="manage-menu-filters">
                        <input type="text" id="menu-search-input" placeholder="Buscar por nombre de producto">
                        <select id="menu-status-select">
                            <option value="all">Todos los estados</option>
                            <option value="active">Activos</option>
                            <option value="inactive">Inactivos</option>
                        </select>
                        <select id="menu-items-per-page-select">
                            <option value="5">5 por página</option>
                            <option value="10">10 por página</option>
                            <option value="15">15 por página</option>
                            <option value="20">20 por página</option>
                        </select>
                        <button id="menu-filter-button">Aplicar Filtros</button>
                    </div>

                    <div class="manage-menu-area">
                        <p>Cargando elementos del menú... o aplica filtros para buscar.</p>
                    </div>

                    <div class="menu-pagination-controls">
                        <button id="menu-prev-page-btn" disabled>Anterior</button>
                        <span id="menu-page-info">Página 0 de 0 (0 elementos en total)</span>
                        <button id="menu-next-page-btn" disabled>Siguiente</button>
                    </div>

                    <div id="menu-item-modal" class="modal">
                        <div class="modal-content">
                            <span class="close-button">&times;</span>
                            <h4 id="menu-item-modal-title">Añadir Elemento al Menú</h4>
                            <form id="menu-item-form" enctype="multipart/form-data">
                                <input type="hidden" id="menu-item-id" name="id">
                                <div>
                                    <label for="menu-item-nombre">Nombre:</label>
                                    <input type="text" id="menu-item-nombre" name="nombre" required>
                                </div>
                                <div>
                                    <label for="menu-item-descripcion">Descripción:</label>
                                    <textarea id="menu-item-descripcion" name="descripcion"></textarea>
                                </div>
                                <div>
                                    <label for="menu-item-precio">Precio:</label>
                                    <input type="number" id="menu-item-precio" name="precio" step="0.01" min="0" required>
                                </div>
                                <div>
                                    <label for="menu-item-foto">Foto:</label>
                                    <input type="file" id="menu-item-foto" name="foto" accept="image/*">
                                    <img id="menu-item-foto-preview" src="#" alt="Vista previa de la foto" style="max-width: 100px; max-height: 100px; display: none; margin-top: 10px;" />
                                    <input type="hidden" id="menu-item-current-foto-ruta" name="current_foto_ruta">
                                </div>
                                <div style="text-align: right;">
                                    <button type="button" id="cancel-menu-item-button">Cancelar</button>
                                    <button type="submit" id="save-menu-item-button">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
                <section id="create-session-section" class="content-section hidden">
                    <h3>Gestión de Sesión de Películas</h3>
                    <div class="main-container">
                        <div class="form-section">
                            {{-- El título "Crear Nueva Sesión" del formulario interno, ya lo tienes arriba. Puedes quitarlo si no lo quieres duplicado --}}
                            <h2>Crear nueva Sesión</h2>

                            <form id="create-session-form">
                                <div>
                                    <label for="session-fecha">Fecha:</label>
                                    <select id="session-fecha" name="fecha" required>
                                        <option value="">Seleccionar fecha</option>
                                        @if(isset($fechas))
                                        @foreach($fechas as $fecha)
                                            <option value="{{ $fecha->id }}">{{ $fecha->fecha }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <label for="session-sala">Sala:</label>
                                    <select id="session-sala" name="sala_id" required>
                                        <option value="">Seleccionar sala</option>
                                        
                                    </select>
                                </div>
                                <div>
                                    <label for="session-pelicula">Película:</label>
                                    <select id="session-pelicula" name="pelicula_id" required>
                                        <option value="">Seleccionar película</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="session-hora">Hora:</label>
                                    <select id="session-hora" name="hora" required>
                                        <option value="">Seleccionar hora</option>
                                    </select>
                                </div>
                                <div>
                                    <button type="submit" class="button primary">Crear Sesión</button>
                                </div>
                                <div id="session-creation-message" style="margin-top: 10px; padding: 10px; text-align: center; font-weight: bold;"></div>
                            </form>
                        </div>

                        <div class="divider"></div>
                        <div class="sessions-table-section">
                            <h2>Sesiones Creadas<span id="selected-session-date"></span></h2>
                            <p id="noSessionsMessage" style="display: none;">No hay sesiones creadas para esta fecha.</p>
                            <table id="sessionsTable" class="sessions-table">
                                <thead>
                                    <tr>
                                        <th>Sesión</th>
                                        <th>Película</th>
                                        <th>Hora</th>
                                        <th>Hora Final</th> 
                                        <th>Sala</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>

</html>