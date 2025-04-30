document.addEventListener("DOMContentLoaded", function () {

    // 1. Obtener referencias a los elementos clave del modal "Mi Cuenta"
    const modalCuenta = document.getElementById("modalCuenta");

    const mostrarCuentaModalBtn = document.getElementById("miCuenta");

    const cerrarCuentaModalBtn = modalCuenta?.querySelector("#cerrarCuentaModal");

    const infoNombre = modalCuenta?.querySelector("#infoNombre");
    const infoApellidos = modalCuenta?.querySelector("#infoApellidos");
    const infoEmail = modalCuenta?.querySelector("#infoEmail");
    const infoFechaNacimiento = modalCuenta?.querySelector("#infoFechaNacimiento");
    const infoTelefono = modalCuenta?.querySelector("#infoTelefono");
    const infoDni = modalCuenta?.querySelector("#infoDni");
    const infoDireccion = modalCuenta?.querySelector("#infoDireccion");
    const infoCiudad = modalCuenta?.querySelector("#infoCiudad");
    const infoCodigoPostal = modalCuenta?.querySelector("#infoCodigoPostal");
    const infoIdDescuento = modalCuenta?.querySelector("#infoIdDescuento");


    // 2. Funciones para controlar la visibilidad del modal "Mi Cuenta"
    function openCuentaModal() {
        if (modalCuenta) {
            modalCuenta.classList.remove('hidden');
            modalCuenta.classList.add('flex');
        } else {
            console.error("ERROR: modalCuenta (#modalCuenta) no encontrado en el DOM. Asegúrate de que principal.blade.php incluye <x-modal.modal_usuario/>");
        }
    }

    function closeCuentaModal() {
        if (modalCuenta) {
            modalCuenta.classList.remove('flex');
            modalCuenta.classList.add('hidden');
            if (infoNombreCompleto) infoNombreCompleto.textContent = '';
            if (infoEmail) infoEmail.textContent = '';
        }
    }

    // Añadir listeners a los elementos de control del modal
    if (cerrarCuentaModalBtn) {
        cerrarCuentaModalBtn.addEventListener("click", closeCuentaModal);
    }



    if (mostrarCuentaModalBtn) {
        mostrarCuentaModalBtn.addEventListener("click", async function (event) {
            event.preventDefault();

            try {
                // 4. Realizar la petición AJAX (fetch) al servidor para obtener los datos del usuario
                const response = await fetch('/perfil/datos', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        // Laravel necesita este header para protegerte contra ataques CSRF, especialmente en peticiones que modifican datos (POST, PUT, DELETE).
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                // 6. Comprobar si la respuesta HTTP del servidor fue exitosa (código 2xx, ej: 200 OK)
                if (!response.ok) {
                    console.error('Error en la respuesta HTTP al obtener datos del usuario:', response.status, response.statusText);
                    alert('No se pudieron cargar los datos de tu perfil. Es posible que tu sesión haya expirado. Por favor, intenta iniciar sesión de nuevo.');
                    if (response.status === 401) {
                        window.location.href = '/login';
                    }
                    return;
                }

                // 7. Parsear la respuesta como JSON
                const userData = await response.json();

                // 8. Rellenar los elementos del modal con los datos recibidos
                
                if (infoNombre) infoNombre.textContent = userData.nombre || ''; //
                if (infoApellidos) infoApellidos.textContent = userData.apellidos || '';
                if (infoEmail) infoEmail.textContent = userData.email || '';
                if (infoFechaNacimiento) infoFechaNacimiento.textContent = userData.fecha_nacimiento || '';
                if (infoTelefono) infoTelefono.textContent = userData.numero_telefono || '';
                if (infoDni) infoDni.textContent = userData.dni || '';
                if (infoDireccion) infoDireccion.textContent = userData.direccion || '';
                if (infoCiudad) infoCiudad.textContent = userData.ciudad || '';
                if (infoCodigoPostal) infoCodigoPostal.textContent = userData.codigo_postal || '';
                if (infoIdDescuento) infoIdDescuento.textContent = userData.id_descuento || 'Ninguno';

                // 9. Abrir el modal "Mi Cuenta" una vez que los datos están cargados y visibles en el modal
                openCuentaModal();

            } catch (error) {
                console.error('Error en la petición fetch (red o parseo) para obtener datos del usuario:', error);
                alert('Ocurrió un error al cargar tus datos de perfil. Por favor, intenta de nuevo más tarde.');
            }
        });
    }


});