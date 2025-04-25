document.addEventListener("DOMContentLoaded", function () {
    // 1. Obtener referencias a los elementos clave
    const modalRegistro = document.getElementById("modalRegistro");
    const checkUrlBase = '/check-';
    const passwordContainers = modalRegistro.querySelectorAll('.password-input-container');

    const cerrarRegistroBtn = modalRegistro.querySelector("#cerrarRegistro");
    const form = modalRegistro.querySelector("form");
    const steps = modalRegistro.querySelectorAll(".form-step");

    // Área para mostrar errores de validación de JavaScript
    const clientSideErrorArea = modalRegistro.querySelector(
        ".client-side-errors"
    );

    passwordContainers.forEach(container => {
        
        const passwordInput = container.querySelector('input[type="password"], input[type="text"]');
        
        const toggleIcon = container.querySelector('.toggle-password');
    
        if (passwordInput && toggleIcon) {
            
            toggleIcon.addEventListener('click', function() {
                
                const currentType = passwordInput.getAttribute('type');
    
                const newType = currentType === 'password' ? 'text' : 'password';
    
                // Cambia el atributo type del input
                passwordInput.setAttribute('type', newType);
    
                toggleIcon.textContent = newType === 'password' ? '👁️' : '🙈';
            });
        }
    });

    let currentStepIndex = 0;

    // 2. Funciones para mostrar/ocultar el modal
    function openModal() {
        modalRegistro.style.display = "flex";
        showStep(0);
    }

    function closeModal() {
        modalRegistro.style.display = "none";
        resetForm();
    }

    // Añadir listeners al botón de cerrar y posiblemente al fondo del modal
    if (cerrarRegistroBtn) {
        cerrarRegistroBtn.addEventListener("click", closeModal);
    }

    const mostrarRegistroBtn = document.getElementById("mostrarRegistro");
    if (mostrarRegistroBtn) {
        mostrarRegistroBtn.addEventListener("click", openModal);
    }

    // 3. Función para mostrar un paso específico
    function showStep(stepIndex) {
        // Asegurarse de que el índice es válido
        if (stepIndex < 0 || stepIndex >= steps.length) {
            console.error("Intentando mostrar un paso inválido:", stepIndex);
            return;
        }

        // Ocultar todos los pasos y remover la clase 'active'
        steps.forEach((step) => {
            step.classList.remove("active");
            step.style.display = "none";
        });

        // Mostrar el paso deseado y añadir la clase 'active'
        steps[stepIndex].classList.add("active");
        steps[stepIndex].style.display = "block";

        currentStepIndex = stepIndex;

        hideErrors();
        clearInvalidClasses();

        // Enfocar el primer campo del nuevo paso para accesibilidad
        const firstInput = steps[stepIndex].querySelector(
            'input:not([type="hidden"]), select, textarea'
        );
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 50); // Pequeño retraso para asegurar que el elemento es visible
        }
    }

    // Funciones para manejar el área de errores de JS
    function showErrors(messages) {
        if (!clientSideErrorArea) return;
        clientSideErrorArea.innerHTML = messages.join("<br>");
        clientSideErrorArea.style.display = "block";
    }

    function hideErrors() {
        if (!clientSideErrorArea) return;
        clientSideErrorArea.innerHTML = "";
        clientSideErrorArea.style.display = "none";
    }

    // Función para quitar las clases 'invalid' de todos los campos en todos los pasos
    function clearInvalidClasses() {
        modalRegistro
            .querySelectorAll(".invalid")
            .forEach((el) => el.classList.remove("invalid"));
    }

    //Función para resetear el formulario y volver al paso 0
    function resetForm() {
        if (form) form.reset();
        showStep(0);
        hideErrors();
        clearInvalidClasses();
        modalRegistro
            .querySelectorAll(".error-message")
            .forEach((el) => (el.style.display = "none"));
    }

    // Establecer el estado inicial al cargar la página
    modalRegistro.style.display = "none";
    // Mostrar el primer paso, aunque el modal esté oculto. Se hará visible al abrir el modal.
    showStep(0);

    // 4. Obtener referencias a los botones de navegación
    // Seleccionamos todos los botones con estas clases dentro del modal
    const nextButtons = modalRegistro.querySelectorAll(".next-step");
    const prevButtons = modalRegistro.querySelectorAll(".prev-step");

    // 5. Añadir listeners a los botones de navegación

    // Botones "Siguiente"
    nextButtons.forEach((button) => {
        button.addEventListener("click", async function () {
            let stepIsValid = true;
            if (currentStepIndex === 0) {
                stepIsValid = await validateStep1(); // Implementaremos esta función
            } else if (currentStepIndex === 1) {
                stepIsValid = await validateStep2(); // Implementaremos esta función
            } else if (currentStepIndex === 2) {
                stepIsValid = await validateStep3(); // Implementaremos esta función
            }
            if (stepIsValid) {
                if (currentStepIndex < steps.length - 1) {
                    showStep(currentStepIndex + 1);
                }
            }
        });
    });

    async function validateStep1() {
    
    hideErrors(); 
    clearInvalidClasses(); 
                clearStepFieldErrors(steps[0]); 
        
        const step1 = steps[0];
    const emailInput = step1.querySelector('[name="email"]');
    const emailConfirmationInput = step1.querySelector(
    '[name="email_confirmation"]'
        );
        const passwordInput = step1.querySelector('[name="password"]');
    const passwordConfirmationInput = step1.querySelector(
    '[name="password_confirmation"]'
    );
        
    let isStepValid = true;
        
     // --- Validaciones para el Paso 1 ---
        
        
        if (!emailInput || !emailInput.value.trim()) {
        displayFieldError(emailInput, "El email es obligatorio.");
        if (emailInput) emailInput.classList.add("invalid");
    isStepValid = false;
        } else if (!/\S+@\S+\.\S+/.test(emailInput.value)) {
    displayFieldError(emailInput, "Por favor, introduce un email válido.");
    if (emailInput) emailInput.classList.add("invalid");
         isStepValid = false;
    } else {
                    clearFieldError(emailInput); 
                }
        
                
                if (!emailConfirmationInput || !emailConfirmationInput.value.trim()) {
                    displayFieldError(emailConfirmationInput, "La confirmación del email es obligatoria.");
                    if (emailConfirmationInput) emailConfirmationInput.classList.add("invalid");
                    isStepValid = false;
                } else if (emailInput && emailConfirmationInput && emailInput.value !== emailConfirmationInput.value) {
                    displayFieldError(emailConfirmationInput, "El email y la confirmación no coinciden.");
                    if (emailInput) emailInput.classList.add("invalid"); 
                    if (emailConfirmationInput) emailConfirmationInput.classList.add("invalid");
                    isStepValid = false;
                } else {
                    clearFieldError(emailConfirmationInput); 
                    
                    if (emailInput) emailInput.classList.remove("invalid");
                }
        
        
        
    if (!passwordInput || !passwordInput.value) {
    displayFieldError(passwordInput, "La contraseña es obligatoria.");
    if (passwordInput) passwordInput.classList.add("invalid");
    isStepValid = false;
    } else if (passwordInput.value.length < 8) {
    displayFieldError(passwordInput, "La contraseña debe tener al menos 8 caracteres.");
    if (passwordInput) passwordInput.classList.add("invalid");
    isStepValid = false;
    } else {
                    clearFieldError(passwordInput);
                }
        
            
                if (!passwordConfirmationInput || !passwordConfirmationInput.value) {
                    displayFieldError(passwordConfirmationInput, "La confirmación de la contraseña es obligatoria.");
                    if (passwordConfirmationInput) passwordConfirmationInput.classList.add("invalid");
                    isStepValid = false;
                } else if (passwordInput && passwordConfirmationInput && passwordInput.value !== passwordConfirmationInput.value) {
                    displayFieldError(passwordConfirmationInput, "La contraseña y la confirmación no coinciden.");
                    if (passwordInput) passwordInput.classList.add("invalid"); 
                    if (passwordConfirmationInput) passwordConfirmationInput.classList.add("invalid");
                    isStepValid = false;
                } else {
                    clearFieldError(passwordConfirmationInput); 
                    
                    if (passwordInput) passwordInput.classList.remove("invalid");
                }

                if (emailInput && !emailInput.classList.contains('invalid')) {
                    const isEmailUnique = await checkEmailExists(emailInput);
                    if (!isEmailUnique) {
                        isStepValid = false; 
                    }
                }

        
        return isStepValid; 
        }

    // Implementación para validar el Paso 2 (errores por campo)
    async function validateStep2() {
    
        hideErrors(); 
        clearInvalidClasses(); 
        clearStepFieldErrors(steps[1]); 

        const step2 = steps[1];

        
        const nombreInput = step2.querySelector('[name="nombre"]');
        const apellidosInput = step2.querySelector('[name="apellidos"]');
        const direccionInput = step2.querySelector('[name="direccion"]');
        const ciudadSelect = step2.querySelector('[name="ciudad"]'); 
        const telefonoInput = step2.querySelector('[name="telefono"]');
        const dniInput = step2.querySelector('[name="dni"]');
        const cpInput = step2.querySelector('[name="codigo_postal"]'); 

        let isStepValid = true; 

        // --- Validaciones para el Paso 2 (usando displayFieldError) ---

        if (!nombreInput || !nombreInput.value.trim()) {
            displayFieldError(nombreInput, "El nombre es obligatorio.");
            if (nombreInput) nombreInput.classList.add("invalid");
            isStepValid = false;
        } else {
            clearFieldError(nombreInput); 
        }

    
        if (!apellidosInput || !apellidosInput.value.trim()) {
            displayFieldError(
                apellidosInput,
                "Los apellidos son obligatorios."
            );
            if (apellidosInput) apellidosInput.classList.add("invalid");
            isStepValid = false;
        } else {
            clearFieldError(apellidosInput); 
        }

        // Validar Dirección (es 'required' en tu HTML)
        if (!direccionInput || !direccionInput.value.trim()) {
            displayFieldError(direccionInput, "La Dirección es obligatoria.");
            if (direccionInput) direccionInput.classList.add("invalid");
            isStepValid = false;
        } else {
            clearFieldError(direccionInput); 
        }

        
        if (!ciudadSelect || !ciudadSelect.value) {
            displayFieldError(ciudadSelect, "Debes seleccionar una ciudad.");
            if (ciudadSelect) ciudadSelect.classList.add("invalid");
            isStepValid = false;
        } else {
            clearFieldError(ciudadSelect); 
        }

        if (!telefonoInput || !telefonoInput.value.trim()) {
            displayFieldError(telefonoInput, "El teléfono es obligatorio.");
            if (telefonoInput) telefonoInput.classList.add("invalid");
            isStepValid = false;
        } else {
            const phoneRegex = /^\d{9}$/; 
            if (!phoneRegex.test(telefonoInput.value.trim())) {
                displayFieldError(
                    telefonoInput,
                    "El teléfono debe tener exactamente 9 dígitos."
                ); 
                if (telefonoInput) telefonoInput.classList.add("invalid");
                isStepValid = false;
            } else {
                clearFieldError(telefonoInput); 
            }
        }

    
        if (!dniInput || !dniInput.value.trim()) {
            displayFieldError(dniInput, "El DNI es obligatorio.");
            if (dniInput) dniInput.classList.add("invalid");
            isStepValid = false;
        } else {
            const dniRegexSoloFormato = /^\d{8}[A-Za-z]$/; 
            if (!dniRegexSoloFormato.test(dniInput.value.trim())) {
                displayFieldError(
                    dniInput,
                    "El formato del DNI debe ser 8 números seguidos de una letra."
                );
                if (dniInput) dniInput.classList.add("invalid");
                isStepValid = false;
            } else {
                clearFieldError(dniInput); 
            }
        }

        if (!cpInput || !cpInput.value.trim()) {
            displayFieldError(cpInput, "El Código Postal es obligatorio.");
            if (cpInput) cpInput.classList.add("invalid");
            isStepValid = false;
        } else {
            const cpRegex = /^\d{5}$/; 
            if (!cpRegex.test(cpInput.value.trim())) {
                displayFieldError(
                    cpInput,
                    "El Código Postal debe tener 5 dígitos."
                ); // Mensaje ajustado
                if (cpInput) cpInput.classList.add("invalid");
                isStepValid = false;
            } else {
                clearFieldError(cpInput); 
            }
        }

        if (dniInput && !dniInput.classList.contains('invalid')) {
            const isDniUnique = await checkDniExists(dniInput);
            if (!isDniUnique) {
                isStepValid = false; 
            }
        }

    

        return isStepValid; 
    }

    // Implementación para validar el Paso 3 

async function validateStep3() {

hideErrors(); 
    clearInvalidClasses();
                clearStepFieldErrors(steps[2]); 
        
const step3 = steps[2]; 
        

    const fechaNacimientoInput = step3.querySelector(
    '[name="fecha_nacimiento"]'
);
const mayorEdadCheckbox = step3.querySelector('[name="mayor_edad"]');
    
        
    let isStepValid = true;
        
    // --- Validaciones para el Paso 3 (usando displayFieldError) ---
        

    if (!fechaNacimientoInput || !fechaNacimientoInput.value.trim()) {
    displayFieldError(fechaNacimientoInput, "La Fecha de Nacimiento es obligatoria.");
if (fechaNacimientoInput)
fechaNacimientoInput.classList.add("invalid");
isStepValid = false;
} else {
    const birthDate = new Date(fechaNacimientoInput.value);
    if (isNaN(birthDate.getTime())) {
displayFieldError(
fechaNacimientoInput,
"Por favor, introduce una fecha de nacimiento válida."
);
if (fechaNacimientoInput)
fechaNacimientoInput.classList.add("invalid");
    isStepValid = false;
} else {
    
        const today = new Date();
    const ageLimitDate = new Date(
today.getFullYear() - 14,
today.getMonth(),
        today.getDate()
);
if (birthDate > ageLimitDate) {
displayFieldError(fechaNacimientoInput, "Debes tener al menos 14 años.");
if (fechaNacimientoInput)
fechaNacimientoInput.classList.add("invalid");
isStepValid = false;
} else {
                             clearFieldError(fechaNacimientoInput); // Limpiar si es válido
                        }
    }
    }
        
        

    if (!mayorEdadCheckbox || !mayorEdadCheckbox.checked) {
                    
                    if (!mayorEdadCheckbox) {
                        console.error("Checkbox 'mayor_edad' no encontrado en el paso 3.");
                    } else {
                         // Si existe pero no está marcado
                        displayFieldError(mayorEdadCheckbox, "Debes confirmar que eres mayor de 14 años."); 
                        mayorEdadCheckbox.classList.add("invalid"); 
                        isStepValid = false;
                    }
    } else {
                    clearFieldError(mayorEdadCheckbox); 
                }
        

        
    return isStepValid; 
    }

    // 6. Manejar el evento submit del formulario para la validación FINAL

    form.addEventListener("submit", function (event) {
        // Detener el envío por defecto del formulario inicialmente
        event.preventDefault();

        // Limpiar errores y clases 'invalid' de validaciones anteriores
        hideErrors();
        clearInvalidClasses();

        const isStep1Valid = validateStep1();
        const isStep2Valid = validateStep2();
        const isStep3Valid = validateStep3();

        // Comprobar si TODOS los pasos son válidos
        const isFormValid = isStep1Valid && isStep2Valid && isStep3Valid;

        if (isFormValid) {
            event.target.submit();
        } else {
            console.log("Validación final del formulario fallida.");

            if (!isStep1Valid) {
                showStep(0);
            } else if (!isStep2Valid) {
                showStep(1);
            } else if (!isStep3Valid) {
                showStep(2);
            }
        }
    });

    // Función auxiliar para encontrar y mostrar un error debajo de un input
    function displayFieldError(inputElement, message) {
        const formRow = inputElement.closest(".form-row");
        if (formRow) {
            const errorElement = formRow.querySelector(".client-side-field-error");
            if (errorElement) {
                errorElement.innerHTML = message;
                errorElement.style.display = "block";
            }else {
                console.warn("ADVERTENCIA: Elemento de error cliente (.client-side-field-error) NO encontrado para input:", inputElement);
        }
} else {
            console.warn("ADVERTENCIA: Form row (.form-row) NO encontrado para input:", inputElement);
    }
        }

    // Función auxiliar para encontrar y limpiar un error debajo de un input
    function clearFieldError(inputElement) {
        const formRow = inputElement.closest(".form-row");
        if (formRow) {
            const errorElement = formRow.querySelector(".client-side-field-error");
            if (errorElement) {
                errorElement.innerHTML = "";
                errorElement.style.display = "none"; // Oculta el elemento si está vacío
            }
        }
    }

    // Función para limpiar TODOS los errores de campo dentro de un paso específico
    function clearStepFieldErrors(stepElement) {
        stepElement
            .querySelectorAll(".form-row .client-side-field-error")
            .forEach((el) => {
                el.innerHTML = "";
                el.style.display = "none";
            });
    }

    async function checkEmailExists(emailInput) {
        if (!emailInput || !emailInput.value.trim()) {
            clearFieldError(emailInput);
            return false;
        }

        const email = emailInput.value.trim();
        
        const errorElement = emailInput.closest('.form-row')?.querySelector('.client-side-field-error');

        if (errorElement && errorElement.innerHTML.includes('ya está registrado')) {
            clearFieldError(emailInput);
        }
        if (emailInput) emailInput.classList.remove("invalid");

        try {
            
            const response = await fetch(`${checkUrlBase}email?email=${encodeURIComponent(email)}`);

            if (!response.ok) {
                console.error('Error en la respuesta del backend al comprobar email:', response.status, response.statusText);
                displayFieldError(emailInput, "Error al verificar email. Intenta de nuevo.");
                if (emailInput) emailInput.classList.add("invalid");
                return false; 
            }

            const data = await response.json();

            if (data.exists) {
                displayFieldError(emailInput, "Este email ya está registrado.");
                if (emailInput) emailInput.classList.add("invalid");
                return false;
            } else {
                clearFieldError(emailInput);
                return true;
            }

        } catch (error) {
            console.error('Error al hacer la petición fetch para comprobar email:', error);
            displayFieldError(emailInput, "Error al verificar email. Intenta de nuevo.");
            if (emailInput) emailInput.classList.add("invalid");
            return false; 
        }
    }


    async function checkDniExists(dniInput) { 
    if (!dniInput || !dniInput.value.trim()) {
    clearFieldError(dniInput); 
    return true; 
    }
    
    const dni = dniInput.value.trim();

    const errorElement = dniInput.closest('.form-row')?.querySelector('.client-side-field-error'); // Asegúrate que la clase del span es correcta
    

    if (errorElement && errorElement.innerHTML.includes('ya existe ese DNI')) { // Ajusta el texto si el mensaje es diferente
    clearFieldError(dniInput);
    }

    if (dniInput) dniInput.classList.remove("invalid");
    
    const dniFormatRegex = /^\d{8}[A-Za-z]$/;
    if (!dniFormatRegex.test(dni)) {
    displayFieldError(dniInput, "El formato del DNI no es válido."); 
    if (dniInput) dniInput.classList.add("invalid");
    return false; 
    }
    
    
    try {
    const response = await fetch(`${checkUrlBase}dni?dni=${encodeURIComponent(dni)}`); 
    
    if (!response.ok) {
    console.error('Error en la respuesta del backend al comprobar DNI:', response.status, response.statusText);
    displayFieldError(dniInput, "Error al verificar DNI. Intenta de nuevo."); 
    if (dniInput) dniInput.classList.add("invalid");
    return false; 
    }
    
    const data = await response.json(); 
    
    if (data.exists) {

    displayFieldError(dniInput, "Ya existe ese DNI registrado."); 
    if (dniInput) dniInput.classList.add("invalid");
    return false; 
    } else {
    
    clearFieldError(dniInput); 
    if (dniInput) dniInput.classList.remove("invalid"); 
    return true; 
    }
    
    } catch (error) {
    console.error('Error al hacer la petición fetch para comprobar DNI:', error);
    displayFieldError(dniInput, "Error al verificar DNI. Intenta de nuevo."); 
    if (dniInput) dniInput.classList.add("invalid");
    return false; 
    }
    }

    // Botones "Anterior"
    prevButtons.forEach((button) => {
        button.addEventListener("click", function () {
            // Al ir hacia atrás, simplemente cambiamos de paso
            if (currentStepIndex > 0) {
                showStep(currentStepIndex - 1);
                // La función showStep ya limpia errores y clases 'invalid'
            }
        });
    });
});
