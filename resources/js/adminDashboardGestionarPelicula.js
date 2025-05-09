// resources/js/adminManageMovies.js

document.addEventListener("DOMContentLoaded", () => {
    // --- Elementos del DOM para "Gestionar Películas" ---
    const manageSearchInput = document.getElementById("manage-search-input");
    const manageGenreSelect = document.getElementById("manage-genre-select");
    const manageStatusSelect = document.getElementById("manage-status-select");
    const manageItemsPerPageSelect = document.getElementById(
        "manage-items-per-page-select"
    );
    const manageFilterButton = document.getElementById("manage-filter-button");
    const manageMoviesArea = document.querySelector(".manage-movies-area");
    const managePageInfoSpan = document.getElementById("manage-page-info");
    const managePrevPageBtn = document.getElementById("manage-prev-page-btn");
    const manageNextPageBtn = document.getElementById("manage-next-page-btn");

    // Variables para "Gestionar Películas"
    let manageCurrentPage = 1;
    let manageMoviesLoaded = false; // Bandera para saber si la lista ya se cargó al menos una vez

    // --- Funciones Auxiliares ---

    // Función para limpiar el área de resultados de gestión y mostrar un mensaje
    const displayManageMessage = (message) => {
        manageMoviesArea.innerHTML = `<p>${message}</p>`;
        updateManagePaginationControls({}); // Resetear paginación
    };

    // Función para mostrar un error en el área de resultados de gestión
    const displayManageError = (
        message = "Ocurrió un error al gestionar películas."
    ) => {
        manageMoviesArea.innerHTML = `<p style="color: red;">${message}</p>`;
        updateManagePaginationControls({}); // Resetear paginación
    };

    // --- Lógica para "Gestionar Películas" ---

    // Función para construir el HTML de un solo ítem de película gestionada
    const buildManagedMovieItemHtml = (movie) => {
        const posterBaseUrl = "https://image.tmdb.org/t/p/w200/";
        const posterUrl = movie.poster_ruta
            ? `${posterBaseUrl}${movie.poster_ruta}`
            : "https://via.placeholder.com/80x120?text=No+Poster";

        const statusButtonText = movie.activa ? "Desactivar" : "Activar";
        const statusButtonClass = movie.activa
            ? "toggle-status-btn deactivate"
            : "toggle-status-btn activate";

        return `
            <div class="managed-movie-item">
                <img src="${posterUrl}" alt="Poster de ${
            movie.titulo || "Película sin título"
        }">
                <div class="movie-details">
                    <h4>${movie.titulo || "Película sin título"} (${
            movie.fecha_estreno
                ? movie.fecha_estreno.split("-")[0]
                : "Año desconocido"
        })</h4>
                    <p>${
                        movie.sinopsis
                            ? movie.sinopsis.substring(0, 150) + "..."
                            : "Sinopsis no disponible."
                    }</p>
                    <p>Estado: <span class="${
                        movie.activa ? "status-active" : "status-inactive"
                    }">${movie.activa ? "Activa" : "Inactiva"}</span></p>
                </div>
                <button class="${statusButtonClass}" data-movie-id="${
            movie.id
        }">${statusButtonText}</button>
            </div>
        `;
    };

    // Función para renderizar la lista de películas gestionadas
    const renderManagedMovies = (paginationData) => {
        manageMoviesArea.innerHTML = "";

        const moviesToDisplay = paginationData.data ?? [];

        if (moviesToDisplay.length === 0) {
            displayManageMessage(
                "No se encontraron películas con los filtros aplicados."
            );
            updateManagePaginationControls(paginationData);
            return;
        }

        moviesToDisplay.forEach((movie) => {
            manageMoviesArea.innerHTML += buildManagedMovieItemHtml(movie);
        });

        // Añadir LISTENERS A LOS BOTONES DE ESTADO después de renderizar
        const toggleStatusButtons =
            manageMoviesArea.querySelectorAll(".toggle-status-btn");

        toggleStatusButtons.forEach((button) => {
            button.addEventListener("click", async (event) => {
                const movieId = button.dataset.movieId;
                const originalButtonText = button.textContent;

                button.disabled = true;
                button.textContent = "Cambiando...";

                try {
                    const response = await fetch(
                        `/administrador/movies/${movieId}/estadoActivo`,
                        {
                            method: "PATCH",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute("content"),
                            },
                        }
                    );

                    const result = await response.json();

                    if (response.ok) {
                        button.textContent = result.new_status
                            ? "Desactivar"
                            : "Activar";
                        button.classList.remove("activate", "deactivate");
                        button.classList.add(
                            result.new_status ? "deactivate" : "activate"
                        );
                        const statusSpan = button
                            .closest(".managed-movie-item")
                            .querySelector(".status-active, .status-inactive");
                        if (statusSpan) {
                            statusSpan.textContent = result.new_status
                                ? "Activa"
                                : "Inactiva";
                            statusSpan.classList.remove(
                                "status-active",
                                "status-inactive"
                            );
                            statusSpan.classList.add(
                                result.new_status
                                    ? "status-active"
                                    : "status-inactive"
                            );
                        }
                    } else {
                        alert(
                            "Error: " +
                                (result.error ||
                                    `Error al cambiar estado (Estado ${response.status}).`)
                        );
                        console.error("Error response from backend:", result);
                        button.textContent = originalButtonText;
                    }
                } catch (error) {
                    console.error(
                        "Error al cambiar estado de película:",
                        error
                    );
                    alert(
                        "Error al intentar cambiar el estado: " + error.message
                    );
                    button.textContent = originalButtonText;
                } finally {
                    button.disabled = false;
                }
            });
        });

        updateManagePaginationControls(paginationData);
    };

    // Función para actualizar el estado de los controles de paginación de Gestión
    const updateManagePaginationControls = (paginationData) => {
        const currentPage = paginationData.current_page ?? 0;
        const lastPage = paginationData.last_page ?? 0;
        const total = paginationData.total ?? 0;
        const perPage = paginationData.per_page ?? 0;

        managePageInfoSpan.textContent = `Página ${currentPage} de ${
            lastPage || 1
        } (${total} películas en total)`;

        managePrevPageBtn.disabled = currentPage <= 1;
        manageNextPageBtn.disabled = currentPage >= lastPage || lastPage === 0;

        // Almacenar la página actual en la variable global para usarla en la siguiente petición
        manageCurrentPage = currentPage;
    };

    // Función para obtener las películas gestionadas desde el backend
    const fetchManagedMovies = async (page = 1) => {
        // Si la lista ya se está cargando, no hacer otra petición
        if (manageFilterButton.disabled) {
            console.log("Carga de películas gestionadas ya en progreso.");
            return;
        }

        // 1. Obtener valores de los filtros de Gestión
        const query = manageSearchInput.value.trim();
        const genreId = manageGenreSelect.value;
        const status = manageStatusSelect.value;
        const itemsPerPage = manageItemsPerPageSelect.value;

        displayManageMessage("Cargando películas...");
        manageFilterButton.disabled = true; // Deshabilitar botón de filtro
        managePrevPageBtn.disabled = true; // Deshabilitar botones de paginación
        manageNextPageBtn.disabled = true;

        try {
            // 2. Construir la URL y parámetros para la llamada AJAX al backend
            const queryParams = new URLSearchParams({
                query: query,
                genre_id: genreId,
                status: status,
                items_per_page: itemsPerPage,
                page: page,
            }).toString();

            const response = await fetch(
                `/administrador/manage-movies?${queryParams}`,
                {
                    // <== URL de la ruta GET
                    method: "GET",
                    headers: { "Content-Type": "application/json" },
                }
            );

            // 3. Procesar la respuesta del backend
            if (!response.ok) {
                let errorMessage = `Error HTTP: ${response.status}`;
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.error || errorMessage;
                } catch (jsonError) {
                    console.error(
                        "Error parsing error response JSON:",
                        jsonError
                    );
                }
                throw new Error(errorMessage);
            }

            const paginationData = await response.json();

            // 4. Renderizar la lista de películas de la página actual
            renderManagedMovies(paginationData);

            // Marcar que las películas gestionadas se cargaron al menos una vez
            manageMoviesLoaded = true;
        } catch (error) {
            console.error("Error fetching managed movies:", error);
            displayManageError(error.message);
            updateManagePaginationControls({}); // Resetear paginación en caso de error
        } finally {
            manageFilterButton.disabled = false; // Habilitar botón de filtro de nuevo
            // Los botones de paginación se habilitan/deshabilitan en updateManagePaginationControls
        }
    };

    // --- Manejo de Eventos de Filtros y Paginación de Gestión ---

    // Listener para el botón de aplicar filtros
    manageFilterButton.addEventListener("click", () => {
        manageCurrentPage = 1; // Resetear a la primera página al aplicar nuevos filtros
        fetchManagedMovies(manageCurrentPage); // Obtener películas con los filtros
    });

    // Listeners para los botones de paginación de Gestión
    managePrevPageBtn.addEventListener("click", () => {
        if (manageCurrentPage > 1) {
            fetchManagedMovies(manageCurrentPage - 1); // Obtener la página anterior
        }
    });

    manageNextPageBtn.addEventListener("click", () => {
        if (!manageNextPageBtn.disabled) {
            fetchManagedMovies(manageCurrentPage + 1); // Obtener la página siguiente
        }
    });

    // --- Escuchar evento para cargar películas gestionadas cuando la sección se muestra ---
    const manageSection = document.getElementById("manage-movies-section");
    if (manageSection) {
        manageSection.addEventListener("sectionShown", (event) => {
            // Verificar que el evento es para esta sección y que aún no se ha cargado
            if (
                event.detail.sectionId === "manage-movies-section" &&
                !manageMoviesLoaded
            ) {
                fetchManagedMovies(); // Cargar la lista al mostrar la sección por primera vez
            }
        });
    }
}); // Cierre de DOMContentLoaded
