document.addEventListener('DOMContentLoaded', () => {
    // --- SELECTORES DE ELEMENTOS ---
    const manageMenuSection = document.getElementById('manage-menu-section');
    const addNewMenuItemButton = document.getElementById('add-new-menu-item-button');
    const menuSearchInput = document.getElementById('menu-search-input');
    const menuStatusSelect = document.getElementById('menu-status-select');
    const menuItemsPerPageSelect = document.getElementById('menu-items-per-page-select');
    const menuFilterButton = document.getElementById('menu-filter-button');
    const manageMenuArea = document.querySelector('.manage-menu-area');
    let menuItemsTableBody;

    const menuPrevPageBtn = document.getElementById('menu-prev-page-btn');
    const menuNextPageBtn = document.getElementById('menu-next-page-btn');
    const menuPageInfo = document.getElementById('menu-page-info');

    const menuItemModal = document.getElementById('menu-item-modal');
    const menuItemModalTitle = document.getElementById('menu-item-modal-title');
    const closeButton = menuItemModal.querySelector('.close-button');
    const menuItemForm = document.getElementById('menu-item-form');
    const menuItemIdInput = document.getElementById('menu-item-id');
    const menuItemNombreInput = document.getElementById('menu-item-nombre');
    const menuItemDescripcionInput = document.getElementById('menu-item-descripcion');
    const menuItemPrecioInput = document.getElementById('menu-item-precio');
    const menuItemFotoInput = document.getElementById('menu-item-foto');
    const menuItemFotoPreview = document.getElementById('menu-item-foto-preview');
    const menuItemCurrentFotoRutaInput = document.getElementById('menu-item-current-foto-ruta');
    const saveMenuItemButton = document.getElementById('save-menu-item-button');
    const cancelMenuItemButton = document.getElementById('cancel-menu-item-button');
    const gestionarMenuCosmosLink = document.getElementById('gestionar-menu-cosmos-link');

    // --- ESTADO ---
    let currentPage = 1;
    let totalPages = 1;
    let totalItems = 0;
    let isEditMode = false;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const defaultImagePath = '/images/menus/imagenEjemplo.jpeg'; // Ruta a tu imagen por defecto en public
    let menuItemsLoaded = false;

    if (gestionarMenuCosmosLink && menuFilterButton) {
        gestionarMenuCosmosLink.addEventListener('click', (event) => {
            event.preventDefault(); // Evita la navegación del enlace si es un <a>
            fetchMenuItems(1); // Llama directamente a la función para cargar los menús
        });
    }

    // --- FUNCIONES DE UI ---
    const displayMenuMessage = (message) => {
        manageMenuArea.innerHTML = `<p>${message}</p>`;
        updatePaginationControls({ current_page: 0, last_page: 0, total: 0 });
    };

    const displayMenuError = (message = "Ocurrió un error al cargar los elementos del menú.") => {
        manageMenuArea.innerHTML = `<p style="color: red;">${message}</p>`;
        updatePaginationControls({ current_page: 0, last_page: 0, total: 0 });
    };

    const buildMenuItemHtml = (item) => {
        const imageUrl = item.imagen_url || defaultImagePath;
        const formattedPrice = item.precio !== null ? `${parseFloat(item.precio).toFixed(2)} €` : 'N/A';
        const statusButtonText = item.activo ? 'Desactivar' : 'Activar';
        const statusButtonClass = item.activo ? 'btn-toggle-status deactivate' : 'btn-toggle-status activate';

        let imageName = '';
        if (item.imagen_url) {
            const parts = item.imagen_url.split('/');
            imageName = parts[parts.length - 1];
        } else {
            const parts = defaultImagePath.split('/');
            imageName = parts[parts.length - 1];
        }

        return `
            <div class="managed-menu-item">
                <img src="${imageUrl}" alt="${item.nombre}" style="width: 100px; height: auto; border-radius: 4px;">
                <div class="menu-item-details">
                    <h4>${item.nombre || 'N/A'}</h4>
                    <p>Precio: ${formattedPrice}</p>
                    <p class='sinopsis'>${item.descripcion ? item.descripcion.substring(0, 100) + '...' : 'Sin descripción'}</p>
                    <p>Estado: <span class="status-text ${item.activo ? 'status-active' : 'status-inactive'}">${item.activo ? 'Activo' : 'Inactivo'}</span></p>
                </div>
                <div class="menu-item-actions">
                    <button class="${statusButtonClass}" data-item-id="${item.id}">${statusButtonText}</button>
                    <button class="btn-edit-item" data-item-id="${item.id}">Editar</button>
                </div>
            </div>
        `;
    };

    // Renderiza los ítems del menú
    function renderMenuItems(items) {
        manageMenuArea.innerHTML = '';
        if (!items || items.length === 0) {
            displayMenuMessage('No se encontraron elementos del menú.');
            return;
        }
        items.forEach(item => {
            manageMenuArea.innerHTML += buildMenuItemHtml(item);
        });

        // Añadir event listeners a los botones dinámicamente creados
        const toggleStatusButtons = manageMenuArea.querySelectorAll('.btn-toggle-status');
        toggleStatusButtons.forEach(button => {
            button.addEventListener('click', () => toggleItemStatus(button.dataset.itemId));
        });

        const editButtons = manageMenuArea.querySelectorAll('.btn-edit-item');
        editButtons.forEach(button => {
            button.addEventListener('click', () => openModalForEdit(button.dataset.itemId));
        });
    }

    // Actualiza los controles de paginación
    function updatePaginationControls(paginationData) {
        const current = paginationData.current_page ?? 0;
        const last = paginationData.last_page ?? 0;
        const total = paginationData.total ?? 0;

        menuPageInfo.textContent = total > 0 ? `Página ${current} de ${last} (${total} elementos)` : `Página 0 de 0 (0 elementos)`;
        menuPrevPageBtn.disabled = current <= 1;
        menuNextPageBtn.disabled = current >= last || last === 0;

        totalPages = last;
        totalItems = total;
        currentPage = current;
    }

    // Carga elementos del menú desde el backend con filtros y paginación
    async function fetchMenuItems(page = 1) {
        const search = menuSearchInput.value.trim();
        const status = menuStatusSelect.value;
        const perPage = parseInt(menuItemsPerPageSelect.value);

        const params = new URLSearchParams({
            page: page,
            perPage: perPage,
        });
        if (search) params.append('search', search);
        if (status !== 'all') params.append('status', status === 'active' ? 1 : 0);

        displayMenuMessage('Cargando elementos del menú...');
        menuFilterButton.disabled = true;
        menuPrevPageBtn.disabled = true;
        menuNextPageBtn.disabled = true;

        try {
            // Nota: Asegúrate de que esta URL sea correcta en tu entorno (ej. /administrador/menu)
            const response = await fetch(`/administrador/menu?${params.toString()}`, {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: 'Error de red' }));
                throw new Error(errorData.message || `Error ${response.status}`);
            }

            const data = await response.json();

            if (data && typeof data.data !== 'undefined') {
                renderMenuItems(data.data);
                updatePaginationControls(data);
            } else if (Array.isArray(data)) {
                renderMenuItems(data);
                updatePaginationControls({
                    current_page: 1,
                    last_page: 1,
                    total: data.length,
                });
                console.warn("Respuesta no paginada detectada.");
            } else {
                renderMenuItems([]);
                updatePaginationControls({ current_page: 0, last_page: 0, total: 0 });
                console.warn("Respuesta inesperada del backend.");
            }
            menuItemsLoaded = true;

        } catch (error) {
            console.error('Error al cargar elementos del menú:', error);
            displayMenuError(`Error al cargar elementos: ${error.message}`);
            updatePaginationControls({ current_page: 0, last_page: 0, total: 0 });
        } finally {
            menuFilterButton.disabled = false;
            menuPrevPageBtn.disabled = false;
            menuNextPageBtn.disabled = false;
        }
    }

    function openModalForAdd() {
        isEditMode = false;
        menuItemForm.reset();
        menuItemIdInput.value = '';
        menuItemCurrentFotoRutaInput.value = '';
        menuItemModalTitle.textContent = 'Añadir Elemento al Menú';
        saveMenuItemButton.textContent = 'Añadir Elemento';
        menuItemFotoPreview.style.display = 'none';
        menuItemFotoPreview.src = '#';
        menuItemModal.style.display = 'flex';
        menuItemNombreInput.focus();
        document.body.classList.add('modal-open'); // Añadir clase al abrir
    }

    async function openModalForEdit(itemId) {
        isEditMode = true;
        menuItemForm.reset();
        menuItemModalTitle.textContent = 'Cargando...';
        menuItemModal.style.display = 'flex';
        document.body.classList.add('modal-open'); // Añadir clase al abrir

        try {
            const response = await fetch(`/administrador/menu/${itemId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            if (!response.ok) throw new Error('No se pudo cargar el elemento.');

            const item = await response.json();
            menuItemModalTitle.textContent = 'Editar Elemento del Menú';
            saveMenuItemButton.textContent = 'Guardar Cambios';

            menuItemIdInput.value = item.id;
            menuItemNombreInput.value = item.nombre || '';
            menuItemDescripcionInput.value = item.descripcion || '';
            menuItemPrecioInput.value = item.precio !== null ? parseFloat(item.precio).toFixed(2) : '';
            menuItemCurrentFotoRutaInput.value = item.imagen_url || '';

            if (item.imagen_url) {
                menuItemFotoPreview.src = item.imagen_url;
                menuItemFotoPreview.style.display = 'block';
            } else {
                menuItemFotoPreview.src = defaultImagePath; // Mostrar default si no hay imagen_url
                menuItemFotoPreview.style.display = 'block'; // O 'none' si prefieres
            }
                menuItemFotoInput.style.display = 'none';
        } catch (error) {
            displayMenuError(`Error al cargar para editar: ${error.message}`);
            closeModal();
        }
    }

    // Cierra el modal
    function closeModal() {
        menuItemModal.style.display = 'none';
        menuItemForm.reset();
    }

    // Maneja el envío del formulario del modal (añadir/editar)
    async function handleFormSubmit(event) {
        event.preventDefault();
        saveMenuItemButton.disabled = true;
        saveMenuItemButton.textContent = isEditMode ? 'Guardando...' : 'Añadiendo...';

        const formData = new FormData(menuItemForm);
        const itemId = menuItemIdInput.value;
        let url = '/administrador/menu';
        let method = 'POST'; // FormData con POST maneja archivos

        if (isEditMode && itemId) {
            url = `/administrador/menu/${itemId}`;
            formData.append('_method', 'PUT'); // Laravel usa _method para simular PUT con POST
        }

        try {
            const response = await fetch(url, {
                method: method,
                body: formData,
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });

            const responseData = await response.json();

            if (!response.ok) {
                if (response.status === 422 && responseData.errors) {
                    let errorMessages = "Errores de validación:\n";
                    for (const field in responseData.errors) {
                        errorMessages += `- ${responseData.errors[field].join(', ')}\n`;
                    }
                    showMessage(errorMessages, 'error');
                } else {
                    throw new Error(responseData.message || `Error ${response.status}`);
                }
            } else {
                showMessage(responseData.message || 'Operación exitosa.', 'success');
                closeModal();
                fetchMenuItems(); // Recargar la lista
            }
        } catch (error) {
            showMessage(`Error al guardar: ${error.message}`, 'error');
        } finally {
            saveMenuItemButton.disabled = false;
            saveMenuItemButton.textContent = isEditMode ? 'Guardar Cambios' : 'Añadir Elemento';
        }
    }

    // Cambia el estado activo/inactivo de un ítem
    async function toggleItemStatus(itemId) {
        const itemDiv = manageMenuArea.querySelector(`.managed-menu-item [data-item-id="${itemId}"]`);
        const button = itemDiv?.closest('.managed-menu-item')?.querySelector('.btn-toggle-status');
        if (!button) return;

        const originalButtonText = button.textContent;
        button.disabled = true;
        button.textContent = "Cambiando...";

        try {
            const response = await fetch(`menu/${itemId}/estadoActivo`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            });

            const result = await response.json();

            if (response.ok) {
                console.log(result.message);
                button.textContent = result.new_status ? 'Desactivar' : 'Activar';
                button.classList.remove('activate', 'deactivate');
                button.classList.add(result.new_status ? 'deactivate' : 'activate');

                const statusSpan = button.closest('.managed-menu-item')?.querySelector('.status-active, .status-inactive');
                if (statusSpan) {
                    statusSpan.textContent = result.new_status ? 'Activo' : 'Inactivo';
                    statusSpan.classList.remove('status-active', 'status-inactive');
                    statusSpan.classList.add(result.new_status ? 'status-active' : 'status-inactive');
                }
            } else {
                alert('Error: ' + (result.error || `Error al cambiar estado (Estado ${response.status}).`));
                console.error('Error response from backend:', result);
                button.textContent = originalButtonText;
            }
        } catch (error) {
            console.error('Error al cambiar estado del menú:', error);
            alert('Error al intentar cambiar el estado: ' + error.message);
            button.textContent = originalButtonText;
        } finally {
            button.disabled = false;
        }
    }

    // Muestra la vista previa de la imagen seleccionada en el formulario
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                menuItemFotoPreview.src = e.target.result;
                menuItemFotoPreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            // Si se deselecciona, mostrar la actual (si es edición) o la default
            const currentPath = menuItemCurrentFotoRutaInput.value;
            menuItemFotoPreview.src = (isEditMode && currentPath) ? currentPath : defaultImagePath;
            menuItemFotoPreview.style.display = (isEditMode && currentPath) || !isEditMode ? 'block' : 'none';
            if (!isEditMode && !file) { // Si es modo añadir y no hay archivo, ocultar preview
                menuItemFotoPreview.style.display = 'none';
                menuItemFotoPreview.src = '#';
            }
        }
    }

    // Función simple para mostrar mensajes (reemplazar con algo mejor)
    function showMessage(message, type = 'success') {
        console.log(`[${type.toUpperCase()}]: ${message}`);
        alert(message);
    }

    // Inicialización de la sección
    function init() {

        menuItemModal.style.display = 'none';
        // Limpiar el manageMenuArea inicialmente
        manageMenuArea.innerHTML = '<p>Cargando elementos del menú...</p>';

        // Listeners
        addNewMenuItemButton.addEventListener('click', openModalForAdd);
        menuFilterButton.addEventListener('click', () => { fetchMenuItems(1);});

        // >>>>>> ESTAS LÍNEAS HAN SIDO DESCOMENTADAS <<<<<<
        menuSearchInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') fetchMenuItems(1); });
        menuStatusSelect.addEventListener('change', () => fetchMenuItems(1));
        menuItemsPerPageSelect.addEventListener('change', () => fetchMenuItems(1));
        // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

        menuPrevPageBtn.addEventListener('click', () => { if (currentPage > 1) fetchMenuItems(currentPage - 1); });
        menuNextPageBtn.addEventListener('click', () => { if (currentPage < totalPages) fetchMenuItems(currentPage + 1); });

        closeButton.addEventListener('click', closeModal);
        cancelMenuItemButton.addEventListener('click', closeModal);
        /* menuItemModal.addEventListener('click', (event) => { if (event.target === menuItemModal) closeModal(); }); */ // Esta línea sigue comentada
        menuItemForm.addEventListener('submit', handleFormSubmit);
        menuItemFotoInput.addEventListener('change', previewImage);

        // Cargar datos iniciales si la sección está visible
        if (manageMenuSection && !manageMenuSection.classList.contains('hidden')) {
            fetchMenuItems();
        }
    }

    // Función para ser llamada cuando la sección se active (si es necesario en tu sistema de tabs)
    window.activateManageMenuSection = () => {
        if (manageMenuSection && !manageMenuSection.classList.contains('hidden') && !menuItemsLoaded) {
            fetchMenuItems();
        }
    };

    // Inicializar
    init();
});