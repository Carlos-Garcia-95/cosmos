document.addEventListener("DOMContentLoaded", function () {
    const modalCuenta = document.getElementById("modalCuenta");
    const mostrarCuentaModalBtn = document.getElementById("miCuenta");
    const cerrarCuentaModalBtn = modalCuenta?.querySelector("#cerrarCuentaModal");

    const editarPerfilBtn = modalCuenta?.querySelector("#editarPerfilBtn");
    const guardarCambiosBtn = modalCuenta?.querySelector("#guardarCambiosBtn");
    const cancelarEdicionBtn = modalCuenta?.querySelector("#cancelarEdicionBtn");

    const infoNombreDisplay = modalCuenta?.querySelector("#infoNombreDisplay");
    const infoNombreEdit = modalCuenta?.querySelector("#infoNombreEdit");
    const infoNombreError = modalCuenta?.querySelector("#infoNombreError");
    const formRowNombre = infoNombreDisplay?.closest('.form-row');
    const infoApellidosDisplay = modalCuenta?.querySelector("#infoApellidosDisplay");
    const infoApellidosEdit = modalCuenta?.querySelector("#infoApellidosEdit");
    const infoApellidosError = modalCuenta?.querySelector("#infoApellidosError");
    const formRowApellidos = infoApellidosDisplay?.closest('.form-row');
    const infoEmailDisplay = modalCuenta?.querySelector("#infoEmailDisplay");
    const infoFechaNacimientoDisplay = modalCuenta?.querySelector("#infoFechaNacimientoDisplay");
    const infoTelefonoDisplay = modalCuenta?.querySelector("#infoTelefonoDisplay");
    const infoTelefonoEdit = modalCuenta?.querySelector("#infoTelefonoEdit");
    const infoTelefonoError = modalCuenta?.querySelector("#infoTelefonoError");
    const formRowTelefono = infoTelefonoDisplay?.closest('.form-row');
    const infoDniDisplay = modalCuenta?.querySelector("#infoDniDisplay");
    const infoDireccionDisplay = modalCuenta?.querySelector("#infoDireccionDisplay");
    const infoDireccionEdit = modalCuenta?.querySelector("#infoDireccionEdit");
    const infoCiudadDisplay = modalCuenta?.querySelector("#infoCiudadDisplay");
    const infoCiudadEdit = modalCuenta?.querySelector("#infoCiudadEdit");
    const infoCiudadError = modalCuenta?.querySelector("#infoCiudadError");
    const formRowCiudad = infoCiudadDisplay?.closest('.form-row');
    const infoCodigoPostalDisplay = modalCuenta?.querySelector("#infoCodigoPostalDisplay");
    const infoCodigoPostalEdit = modalCuenta?.querySelector("#infoCodigoPostalEdit");
    const infoCodigoPostalError = modalCuenta?.querySelector("#infoCodigoPostalError");
    const formRowCodigoPostal = infoCodigoPostalDisplay?.closest('.form-row');
    const infoIdDescuentoDisplay = modalCuenta?.querySelector("#infoIdDescuentoDisplay");

    const serverSideErrorsArea = modalCuenta?.querySelector(".server-side-errors ul");
    const serverSideErrorsContainer = modalCuenta?.querySelector(".server-side-errors");

    const profileUpdateSuccessMessage = modalCuenta.querySelector("#profileUpdateSuccessMessage");

    let currentUserCityId = null;

    function openCuentaModal() {
        if (modalCuenta) {
            modalCuenta.classList.remove('hidden');
            modalCuenta.classList.add('flex');
            exitEditMode();
        }
    }

    function closeCuentaModal() {
        if (modalCuenta) {
            modalCuenta.classList.remove('flex');
            modalCuenta.classList.add('hidden');
            exitEditMode();
            clearFieldErrors();
            clearServerErrors();
        }
    }

    function enterEditMode() {
        if (modalCuenta) {
            modalCuenta.classList.add('is-editing');
            editarPerfilBtn.style.display = 'none';
            guardarCambiosBtn.style.display = 'inline-block';
            cancelarEdicionBtn.style.display = 'inline-block';

            infoNombreEdit.style.display = 'inline-block';
            infoApellidosEdit.style.display = 'inline-block';
            infoTelefonoEdit.style.display = 'inline-block';
            infoDireccionEdit.style.display = 'inline-block';
            infoCodigoPostalEdit.style.display = 'inline-block';
            infoCiudadEdit.style.display = 'inline-block';

            infoNombreDisplay.style.display = 'none';
            infoApellidosDisplay.style.display = 'none';
            infoTelefonoDisplay.style.display = 'none';
            infoDireccionDisplay.style.display = 'none';
            infoCodigoPostalDisplay.style.display = 'none';
            infoCiudadDisplay.style.display = 'none';

            if (infoNombreEdit && infoNombreDisplay) infoNombreEdit.value = infoNombreDisplay.textContent.trim();
            if (infoApellidosEdit && infoApellidosDisplay) infoApellidosEdit.value = infoApellidosDisplay.textContent.trim();
            if (infoTelefonoEdit && infoTelefonoDisplay) infoTelefonoEdit.value = infoTelefonoDisplay.textContent.trim();
            if (infoDireccionEdit && infoDireccionDisplay) infoDireccionEdit.value = infoDireccionDisplay.textContent.trim();
            if (infoCodigoPostalEdit && infoCodigoPostalDisplay) infoCodigoPostalEdit.value = infoCodigoPostalDisplay.textContent.trim();

            if (infoCiudadEdit) populateCitiesSelect(infoCiudadEdit, currentUserCityId);

            clearFieldErrors();
            clearServerErrors();
        }
    }

    function exitEditMode() {
        if (modalCuenta) {
            modalCuenta.classList.remove('is-editing');
            editarPerfilBtn.style.display = 'inline-block';
            guardarCambiosBtn.style.display = 'none';
            cancelarEdicionBtn.style.display = 'none';
            
            infoNombreEdit.style.display = 'none';
            infoApellidosEdit.style.display = 'none';
            infoTelefonoEdit.style.display = 'none';
            infoDireccionEdit.style.display = 'none';
            infoCodigoPostalEdit.style.display = 'none';
            infoCiudadEdit.style.display = 'none';

            infoNombreDisplay.style.display = 'inline-block';
            infoApellidosDisplay.style.display = 'inline-block';
            infoTelefonoDisplay.style.display = 'inline-block';
            infoDireccionDisplay.style.display = 'inline-block';
            infoCodigoPostalDisplay.style.display = 'inline-block';
            infoCiudadDisplay.style.display = 'inline-block';

            clearFieldErrors();
            clearServerErrors();
        }
    }

    function validateNoNumbers(value, errorElement, formRowElement, errorMessage) {
        const regex = /^[a-zA-ZÀ-ÖØ-öø-ÿ\s\.\-]*$/;
        if (!regex.test(value) || value.trim() === '') {
            displayFieldError(errorElement, formRowElement, errorMessage);
            return false;
        }
        clearFieldError(errorElement, formRowElement);
        return true;
    }

    function validateFiveDigitsNumeric(value, errorElement, formRowElement, errorMessage) {
        const regex = /^\d{5}$/;
        const cleanedValue = value.replace(/\s/g, '');
        if (!regex.test(cleanedValue)) {
            displayFieldError(errorElement, formRowElement, errorMessage);
            return false;
        }
        clearFieldError(errorElement, formRowElement);
        return true;
    }

    function validateNineDigitsNumeric(value, errorElement, formRowElement, errorMessage) {
        const regex = /^\d{9}$/;
        const cleanedValue = value.replace(/\s/g, '');
        if (!regex.test(cleanedValue)) {
            displayFieldError(errorElement, formRowElement, errorMessage);
            return false;
        }
        clearFieldError(errorElement, formRowElement);
        return true;
    }

    function displayFieldError(errorElement, formRowElement, message) {
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            const inputElement = errorElement.previousElementSibling;
            if (inputElement && inputElement.classList) {
                inputElement.classList.add('invalid');
            }
            if (formRowElement) {
                formRowElement.classList.add('has-error');
            } else {
                const foundFormRow = errorElement.closest('.form-row');
                if (foundFormRow) foundFormRow.classList.add('has-error');
            }
        }
    }

    function clearFieldError(errorElement, formRowElement) {
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
            const inputElement = errorElement.previousElementSibling;
            if (inputElement && inputElement.classList) {
                inputElement.classList.remove('invalid');
            }
            if (formRowElement) {
                formRowElement.classList.remove('has-error');
            } else {
                const foundFormRow = errorElement.closest('.form-row');
                if (foundFormRow) foundFormRow.classList.remove('has-error');
            }
        }
    }

    function clearFieldErrors() {
        modalCuenta?.querySelectorAll('.client-side-field-error').forEach(errorEl => {
            const formRow = errorEl.closest('.form-row');
            clearFieldError(errorEl, formRow);
        });
    }

    function displayServerErrors(errors) {
        if (serverSideErrorsArea && serverSideErrorsContainer) {
            serverSideErrorsArea.innerHTML = '';
            const errorsList = Array.isArray(errors) ? errors : [JSON.stringify(errors)];

            if (errorsList.length > 0) {
                errorsList.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    serverSideErrorsArea.appendChild(li);
                });
                serverSideErrorsContainer.style.display = 'block';
            } else {
                serverSideErrorsContainer.style.display = 'none';
            }
        }
    }

    function clearServerErrors() {
        if (serverSideErrorsArea && serverSideErrorsContainer) {
            serverSideErrorsArea.innerHTML = '';
            serverSideErrorsContainer.style.display = 'none';
        }
    }

    async function populateCitiesSelect(selectElement, selectedCityId = null) {
        if (!selectElement) return;

        selectElement.innerHTML = '<option value="">Cargando ciudades...</option>';
        selectElement.disabled = true;

        try {
            const response = await fetch('/ciudades', {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                console.error('Error al obtener lista de ciudades:', response.status, response.statusText);
                selectElement.innerHTML = '<option value="">Error al cargar ciudades</option>';
                return;
            }

            const cities = await response.json();

            selectElement.innerHTML = '<option value="">Selecciona una ciudad</option>';

            if (Array.isArray(cities)) {
                cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.id;
                    option.textContent = city.nombre;
                    if (selectedCityId !== null && city.id == selectedCityId) {
                        option.selected = true;
                    }
                    selectElement.appendChild(option);
                });
            }

            selectElement.disabled = false;

        } catch (error) {
            console.error('Error en fetch para obtener ciudades:', error);
            selectElement.innerHTML = '<option value="">Error al cargar ciudades</option>';
        }
    }

    if (mostrarCuentaModalBtn) {
        mostrarCuentaModalBtn.addEventListener("click", async function (event) {
            event.preventDefault();

            clearFieldErrors();
            clearServerErrors();

            try {
                const response = await fetch('/perfil/datos', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    console.error('Error al obtener datos del usuario:', response.status, response.statusText);
                    alert('No se pudieron cargar los datos del perfil. Por favor, intenta iniciar sesión de nuevo.');
                    closeCuentaModal();
                    return;
                }

                const userData = await response.json();
                console.log('Datos del usuario recibidos para visualización/edición:', userData);

                if (infoNombreDisplay) infoNombreDisplay.textContent = userData.nombre || '';
                if (infoApellidosDisplay) infoApellidosDisplay.textContent = userData.apellidos || '';
                if (infoEmailDisplay) infoEmailDisplay.textContent = userData.email || '';
                if (infoFechaNacimientoDisplay) infoFechaNacimientoDisplay.textContent = userData.fecha_nacimiento || '';
                if (infoTelefonoDisplay) infoTelefonoDisplay.textContent = userData.numero_telefono || '';
                if (infoDniDisplay) infoDniDisplay.textContent = userData.dni || '';
                if (infoDireccionDisplay) infoDireccionDisplay.textContent = userData.direccion || '';
                if (infoCiudadDisplay) infoCiudadDisplay.textContent = userData.ciudad || '';
                if (infoCodigoPostalDisplay) infoCodigoPostalDisplay.textContent = userData.codigo_postal || '';
                if (infoIdDescuentoDisplay) infoIdDescuentoDisplay.textContent = userData.id_descuento || 'Ninguno';

                currentUserCityId = userData.ciudad_id;


                openCuentaModal();

            } catch (error) {
                console.error('Error en la petición fetch para obtener datos del usuario:', error);
                alert('Ocurrió un error al cargar tus datos. Intenta de nuevo más tarde.');
                closeCuentaModal();
            }
        });
    } else {
        console.warn("ADVERTENCIA: Botón '#miCuenta' no encontrado.");
    }

    if (editarPerfilBtn) {
        editarPerfilBtn.addEventListener('click', enterEditMode);
    }

    if (cancelarEdicionBtn) {
        cancelarEdicionBtn.addEventListener('click', exitEditMode);
    }

    if (guardarCambiosBtn) {
        guardarCambiosBtn.addEventListener('click', async function (event) {
            event.preventDefault();

            clearFieldErrors();
            clearServerErrors();

            let isClientValid = true;

            if (infoNombreEdit && formRowNombre) {
                if (!validateNoNumbers(infoNombreEdit.value, infoNombreError, formRowNombre, 'El nombre solo debe contener letras.')) {
                    isClientValid = false;
                }
            }

            if (infoApellidosEdit && formRowApellidos) {
                if (!validateNoNumbers(infoApellidosEdit.value, infoApellidosError, formRowApellidos, 'Los apellidos solo deben contener letras.')) {
                    isClientValid = false;
                }
            }

            if (infoTelefonoEdit && formRowTelefono) {
                if (!validateNineDigitsNumeric(infoTelefonoEdit.value, infoTelefonoError, formRowTelefono, 'El teléfono debe tener exactamente 9 números.')) {
                    isClientValid = false;
                }
            }

            if (infoCodigoPostalEdit && formRowCodigoPostal) {
                if (!validateFiveDigitsNumeric(infoCodigoPostalEdit.value, infoCodigoPostalError, formRowCodigoPostal, 'El código postal debe tener exactamente 5 números.')) {
                    isClientValid = false;
                }
            }

            if (infoCiudadEdit && formRowCiudad) {
                if (infoCiudadEdit.value === "") {
                    displayFieldError(infoCiudadError, formRowCiudad, 'Debes seleccionar una ciudad.');
                    isClientValid = false;
                } else {
                    clearFieldError(infoCiudadError, formRowCiudad);
                }
            }


            if (!isClientValid) {
                console.log('>>> Validación cliente fallida.');
                return;
            }

            console.log('>>> Validación cliente pasada. Enviando datos al servidor...');

            const updatedData = {
                nombre: infoNombreEdit?.value.trim() || '',
                apellidos: infoApellidosEdit?.value.trim() || '',
                numero_telefono: infoTelefonoEdit?.value.trim().replace(/\s/g, '') || '',
                direccion: infoDireccionEdit?.value.trim() || '',
                ciudad_id: infoCiudadEdit?.value || '',
                codigo_postal: infoCodigoPostalEdit?.value.trim().replace(/\s/g, '') || '',
            };

            console.log('Datos a enviar al backend PATCH /perfil/update:', updatedData);

            try {
                const response = await fetch('/perfil/modificar', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(updatedData)
                });

                const responseData = await response.json();

                if (response.ok) {
                    console.log('>>> Petición PATCH exitosa:', responseData);
                    if (profileUpdateSuccessMessage) {
                        profileUpdateSuccessMessage.textContent = responseData.message || 'Perfil actualizado correctamente.';
                        profileUpdateSuccessMessage.classList.add('is-visible');
                        
                        setTimeout(() => {
                            profileUpdateSuccessMessage.classList.remove('is-visible');
                            setTimeout(() => {
                                profileUpdateSuccessMessage.textContent = '';
                            }, 500);
                        }, 3000);
                    }
                    if (responseData.user) {
                        const updatedUser = responseData.user;
                        console.log('Datos de usuario actualizados recibidos del backend:', updatedUser);

                        if (infoNombreDisplay) infoNombreDisplay.textContent = updatedUser.nombre || '';
                        if (infoApellidosDisplay) infoApellidosDisplay.textContent = updatedUser.apellidos || '';
                        if (infoTelefonoDisplay) infoTelefonoDisplay.textContent = updatedUser.numero_telefono || '';
                        if (infoDireccionDisplay) infoDireccionDisplay.textContent = updatedUser.direccion || '';
                        if (infoCiudadDisplay) infoCiudadDisplay.textContent = updatedUser.ciudad || '';
                        if (infoCodigoPostalDisplay) infoCodigoPostalDisplay.textContent = updatedUser.codigo_postal || '';

                        if (updatedUser.ciudad_id !== undefined) currentUserCityId = updatedUser.ciudad_id;
                    } else {
                        console.warn("Backend no devolvió datos de usuario actualizados en la respuesta.");
                    }

                    exitEditMode();

                } else if (response.status === 422) {
                    console.error('>>> Error de validación del servidor:', responseData);
                    if (responseData.errors) {
                        for (const field in responseData.errors) {
                            const errorMessages = responseData.errors[field];

                            let errorElementId = null;
                            if (field === 'nombre') errorElementId = 'infoNombreError';
                            else if (field === 'apellidos') errorElementId = 'infoApellidosError';
                            else if (field === 'numero_telefono') errorElementId = 'infoTelefonoError';
                            else if (field === 'direccion') errorElementId = 'infoDireccionError';
                            else if (field === 'ciudad_id') errorElementId = 'infoCiudadError';
                            else if (field === 'codigo_postal') errorElementId = 'infoCodigoPostalError';

                            const errorElement = modalCuenta?.querySelector(`#${errorElementId}`);
                            const formRowElement = errorElement?.closest('.form-row');


                            if (errorElement && Array.isArray(errorMessages) && errorMessages.length > 0) {
                                displayFieldError(errorElement, formRowElement, errorMessages.join(' '));
                            } else {
                                console.warn(`Elemento de error para el campo '${field}' no encontrado.`);
                                displayServerErrors([`Error en el campo ${field}: ${errorMessages.join(' ')}`]);
                            }
                        }
                    } else {
                        console.error('Error 422 con formato de respuesta inesperado:', responseData);
                        displayServerErrors([responseData.message || 'Error de validación. Por favor, revisa tus datos.']);
                    }

                } else {
                    console.error('>>> Error del servidor:', response.status, response.statusText, responseData);
                    displayServerErrors([responseData.message || `Ocurrió un error al guardar los cambios (Código: ${response.status}).`]);
                }

            } catch (error) {
                console.error('>>> Error en la petición fetch para guardar cambios:', error);
                displayServerErrors(['Ocurrió un error de conexión o el servidor no responde. Por favor, intenta de nuevo.']);

            } finally {
            }
        });
    }

    function capitalizeFirstLetter(string) {
        if (!string) return '';
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    if (cerrarCuentaModalBtn) {
        cerrarCuentaModalBtn.addEventListener("click", closeCuentaModal);
    }
    modalCuenta?.addEventListener('click', function (event) {
        if (event.target === modalCuenta) {
            closeCuentaModal();
        }
    });
});