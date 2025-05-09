document.addEventListener('DOMContentLoaded', () => {

    const sidebarLinks = document.querySelectorAll('.sidebar-link[data-section]');
    const contentSections = document.querySelectorAll('.content-section');

    const searchInput = document.getElementById('api-search-input');
    const listTypeSelect = document.getElementById('api-list-type-select');
    const genreSelect = document.getElementById('api-genre-select');
    const quantitySelect = document.getElementById('api-quantity-select');
    const languageSelect = document.getElementById('api-language-select');

    const searchButton = document.getElementById('api-search-button');
    const resultsArea = document.querySelector('.api-results-area');
    const pageInfoSpan = document.getElementById('page-info');
    const prevPageBtn = document.getElementById('prev-page-btn');
    const nextPageBtn = document.getElementById('next-page-btn');

    const updateSearchInputState = () => {
        if (listTypeSelect.value === 'search') {
            searchInput.disabled = false;
            searchInput.placeholder = 'Buscar por título';
        } else {
            searchInput.disabled = true;
            searchInput.value = '';
            searchInput.placeholder = 'Búsqueda por título deshabilitada';
        }
    };

    listTypeSelect.addEventListener('change', updateSearchInputState);
    updateSearchInputState();

    let allFetchedMovies = [];
    let currentPage = 1;
    const itemsPerPage = 5;

    const displayMessage = (message) => {
        resultsArea.innerHTML = `<p>${message}</p>`;
        updatePaginationControls(0);
    };

    const displayError = (message = 'Ocurrió un error al buscar películas.') => {
        resultsArea.innerHTML = `<p style="color: red;">${message}</p>`;
        updatePaginationControls(0);
    };

    const updatePaginationControls = (totalMoviesCount) => {
        const totalDisplayPages = Math.ceil(totalMoviesCount / itemsPerPage);
        pageInfoSpan.textContent = `Página ${currentPage} de ${totalDisplayPages || 1}`;

        prevPageBtn.disabled = currentPage === 1;
        nextPageBtn.disabled = currentPage === totalDisplayPages || totalDisplayPages === 0;
    };

    const buildMovieItemHtml = (movie) => {
        const posterBaseUrl = 'https://image.tmdb.org/t/p/w200/';
        const posterUrl = movie.poster_path ? `${posterBaseUrl}${movie.poster_path}` : 'https://via.placeholder.com/80x120?text=No+Poster';

        const buttonText = movie.is_added ? 'Añadida' : 'Añadir pelicula';
        const buttonDisabled = movie.is_added ? 'disabled' : '';
        const buttonClass = movie.is_added ? 'add-movie-btn added' : 'add-movie-btn';

        return `
            <div class="api-movie-item">
                <img src="${posterUrl}" alt="Poster de ${movie.title || 'Película sin título'}">
                <div class="movie-details">
                    <h4>${movie.title || 'Película sin título'} (${movie.release_date ? movie.release_date.split('-')[0] : 'Año desconocido'})</h4>
                    <p>${movie.overview ? movie.overview.substring(0, 150) + '...' : 'Sinopsis no disponible.'}</p>
                </div>
                <button class="${buttonClass}" data-tmdb-id="${movie.id}" ${buttonDisabled}>${buttonText}</button>
            </div>
        `;
    };

    const renderCurrentPageMovies = () => {
        resultsArea.innerHTML = '';

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const moviesToDisplay = allFetchedMovies.slice(startIndex, endIndex);

        if (moviesToDisplay.length === 0 && allFetchedMovies.length > 0) {
            currentPage = Math.max(1, currentPage - 1);
            renderCurrentPageMovies();
            return;
        }

        moviesToDisplay.forEach(movie => {
            resultsArea.innerHTML += buildMovieItemHtml(movie);
        });

        const addMovieButtons = resultsArea.querySelectorAll('.add-movie-btn:not(.added)');

        addMovieButtons.forEach(button => {
            button.addEventListener('click', async (event) => {
                const tmdbId = button.dataset.tmdbId;

                button.disabled = true;
                const originalButtonText = button.textContent;
                button.textContent = 'Añadiendo...';

                try {
                    const response = await fetch('/administrador/movies', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ tmdb_id: tmdbId })
                    });

                    const result = await response.json();

                    if (response.ok) {
                        if (response.status === 409) {
                            button.textContent = 'Ya añadida';
                             button.classList.add('added'); // Add 'added' class for styling
                        } else if (result.status === 'success') {
                            button.textContent = 'Añadida';
                            button.classList.add('added'); // Add 'added' class for styling
                        } else if (result.status === 'duplicate') {
                            button.textContent = 'Ya añadida';
                             button.classList.add('added'); // Add 'added' class for styling
                        } else {
                            console.warn('Respuesta exitosa con estado desconocido:', result);
                            alert(result.message || 'Operación completada con estado desconocido.');
                            button.textContent = originalButtonText;
                            button.disabled = false;
                            button.classList.remove('added');
                        }
                    } else {
                        alert('Error: ' + (result.error || `Error desconocido en el servidor (Estado ${response.status}).`));
                        console.error('Error response from backend:', result);
                        button.textContent = originalButtonText;
                        button.disabled = false;
                        button.classList.remove('added');
                    }

                } catch (error) {
                    console.error('Error al añadir película:', error);
                    alert('Error al intentar añadir la película: ' + error.message);
                    button.textContent = originalButtonText;
                    button.disabled = false;
                    button.classList.remove('added');
                }
            });
        });

        updatePaginationControls(allFetchedMovies.length);
    };

    prevPageBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderCurrentPageMovies();
        }
    });

    nextPageBtn.addEventListener('click', () => {
        const totalDisplayPages = Math.ceil(allFetchedMovies.length / itemsPerPage);
        if (currentPage < totalDisplayPages) {
            currentPage++;
            renderCurrentPageMovies();
        }
    });

    const performSearch = async () => {
        const query = searchInput.value.trim();
        const listType = listTypeSelect.value;
        const genreId = genreSelect.value;
        const pagesToFetch = quantitySelect.value;
        const searchLanguage = languageSelect.value;

        if (listType === 'search' && query === '') {
            displayMessage('Por favor, introduce un título para buscar.');
            allFetchedMovies = [];
            currentPage = 1;
            updatePaginationControls(0);
            return;
        }

        displayMessage('Buscando películas...');
        searchButton.disabled = true;
        prevPageBtn.disabled = true;
        nextPageBtn.disabled = true;

        try {
            const queryParams = new URLSearchParams({
                query: query,
                list_type: listType,
                genre_id: genreId,
                pages_to_fetch: pagesToFetch,
                language: searchLanguage
            }).toString();

            const response = await fetch(`/administrador/buscar-peliculas-api?${queryParams}`, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' },
            });

            if (!response.ok) {
                const errorData = await response.json();
                const errorMessage = errorData.error || `Error HTTP: ${response.status}`;
                throw new Error(errorMessage);
            }

            const movies = await response.json();

            allFetchedMovies = movies;
            currentPage = 1;

            renderCurrentPageMovies();

        } catch (error) {
            console.error('Error fetching movies:', error);
            displayError(error.message);
            allFetchedMovies = [];
        } finally {
            searchButton.disabled = false;
        }
    };

    searchButton.addEventListener('click', performSearch);

    // --- Lógica para el Cambio de Secciones de la Barra Lateral ---

    // Función para mostrar la sección correcta y actualizar el enlace activo
    const showSection = (sectionId) => {
        contentSections.forEach(section => {
            section.classList.add('hidden');
        });

        // Mostrar la sección solicitada
        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
            targetSection.classList.remove('hidden');

            targetSection.dispatchEvent(new CustomEvent('sectionShown', { detail: { sectionId: sectionId } }));
        }

        // Remover la clase 'active' de todos los enlaces de la barra lateral
        sidebarLinks.forEach(link => {
            link.classList.remove('active');
        });

        // Añadir la clase 'active' al enlace que corresponde a la sección mostrada
        const activeLink = document.querySelector(`.sidebar-link[data-section="${sectionId.replace('-section', '')}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }

        localStorage.setItem('activeAdminSection', sectionId);
    };

    // Añadir listeners a los enlaces de la barra lateral
    sidebarLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const sectionId = link.dataset.section + '-section';
            showSection(sectionId);
        });
    });

    // Mostrar la sección activa al cargar la página (por defecto o la última visitada)
    const savedSectionId = localStorage.getItem('activeAdminSection');
    if (savedSectionId && document.getElementById(savedSectionId)) {
        showSection(savedSectionId);
    } else {
        showSection('add-movies-section');
    }

});
