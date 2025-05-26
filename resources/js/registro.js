document.addEventListener("DOMContentLoaded", function () {
    const modalRegistro = document.getElementById("modalRegistro");
    const checkUrlBase = "/check-";
    const passwordContainers = modalRegistro.querySelectorAll(
        ".password-input-container"
    );
    const mostrarRegistroBt = document.getElementById("mostrarRegistro");

    const cerrarRegistroBtn = modalRegistro.querySelector("#cerrarRegistro");
    const form = modalRegistro.querySelector("form");
    const steps = modalRegistro.querySelectorAll(".form-step");

    const clientSideErrorArea = modalRegistro.querySelector(
        ".client-side-errors"
    );

    function calculateDniLetter(dniNumber) {
        const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
        const number = parseInt(dniNumber, 10);
        if (isNaN(number)) {
            return null;
        }
        return letras[number % 23];
    }

    function isValidDniLetter(dni) {
        const dniRegex = /^\d{8}[A-Za-z]$/;
        if (!dniRegex.test(dni)) {
            return false;
        }

        const numberPart = dni.substring(0, 8);
        const letterPart = dni.substring(8).toUpperCase();
        const calculatedLetter = calculateDniLetter(numberPart);

        return calculatedLetter !== null && calculatedLetter === letterPart;
    }

    passwordContainers.forEach((container) => {
        const passwordInput = container.querySelector(
            'input[type="password"], input[type="text"]'
        );
        const toggleIcon = container.querySelector(".toggle-password");

        if (passwordInput && toggleIcon) {
            toggleIcon.addEventListener("click", function () {
                const currentType = passwordInput.getAttribute("type");
                const newType =
                    currentType === "password" ? "text" : "password";
                passwordInput.setAttribute("type", newType);
                toggleIcon.textContent = newType === "password" ? "👁️" : "🙈";
            });
        }
    });

    let currentStepIndex = 0;

    function openModal() {
        const modalRegistro = document.getElementById("modalRegistro");

        if (modalRegistro) {
            modalRegistro.classList.remove("hidden");
            modalRegistro.classList.add("flex");

            showStep(0);

            const firstInput = modalRegistro.querySelector(
                'input:not([type="hidden"]), select, textarea'
            );
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 50);
            }
        }
    }

    function closeModal() {
        const modalRegistro = document.getElementById("modalRegistro");

        if (modalRegistro) {
            modalRegistro.classList.remove("flex");
            modalRegistro.classList.add("hidden");
            resetForm();
            hideErrors();
            clearInvalidClasses();
            modalRegistro
                .querySelectorAll(".error-messages")
                .forEach((el) => (el.style.display = "none"));
        }
    }

    if (cerrarRegistroBtn) {
        cerrarRegistroBtn.addEventListener("click", closeModal);
    }

    const mostrarRegistroBtn = document.getElementById("mostrarRegistro");
    if (mostrarRegistroBtn) {
        mostrarRegistroBtn.addEventListener("click", openModal);
    }

    function showStep(stepIndex) {
        if (stepIndex < 0 || stepIndex >= steps.length) {
            return;
        }

        steps.forEach((step) => {
            step.classList.remove("active");
            step.style.display = "none";
        });

        steps[stepIndex].classList.add("active");
        steps[stepIndex].style.display = "block";

        currentStepIndex = stepIndex;

        hideErrors();
        clearInvalidClasses();

        const firstInput = steps[stepIndex].querySelector(
            'input:not([type="hidden"]), select, textarea'
        );
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 50);
        }
    }

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

    function clearInvalidClasses() {
        modalRegistro
            .querySelectorAll(".invalid")
            .forEach((el) => el.classList.remove("invalid"));
    }

    function resetForm() {
        if (form) form.reset();
        showStep(0);
        hideErrors();
        clearInvalidClasses();
        modalRegistro
            .querySelectorAll(".error-message")
            .forEach((el) => (el.style.display = "none"));
    }

    modalRegistro.style.display = "none";
    showStep(0);

    const nextButtons = modalRegistro.querySelectorAll(".next-step");
    const prevButtons = modalRegistro.querySelectorAll(".prev-step");

    nextButtons.forEach((button) => {
        button.addEventListener("click", async function () {
            let stepIsValid = true;
            if (currentStepIndex === 0) {
                stepIsValid = await validateStep1();
            } else if (currentStepIndex === 1) {
                stepIsValid = await validateStep2();
            } else if (currentStepIndex === 2) {
                stepIsValid = await validateStep3();
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

        if (!emailInput || !emailInput.value.trim()) {
            displayFieldError(emailInput, "El email es obligatorio.");
            if (emailInput) emailInput.classList.add("invalid");
            isStepValid = false;
        } else if (!/\S+@\S+\.\S+/.test(emailInput.value)) {
            displayFieldError(
                emailInput,
                "Por favor, introduce un email válido."
            );
            if (emailInput) emailInput.classList.add("invalid");
            isStepValid = false;
        } else {
            clearFieldError(emailInput);
        }

        if (!emailConfirmationInput || !emailConfirmationInput.value.trim()) {
            displayFieldError(
                emailConfirmationInput,
                "La confirmación del email es obligatoria."
            );
            if (emailConfirmationInput)
                emailConfirmationInput.classList.add("invalid");
            isStepValid = false;
        } else if (
            emailInput &&
            emailConfirmationInput &&
            emailInput.value !== emailConfirmationInput.value
        ) {
            displayFieldError(
                emailConfirmationInput,
                "El email y la confirmación no coinciden."
            );
            if (emailInput) emailInput.classList.add("invalid");
            if (emailConfirmationInput)
                emailConfirmationInput.classList.add("invalid");
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
            displayFieldError(
                passwordInput,
                "La contraseña debe tener al menos 8 caracteres."
            );
            if (passwordInput) passwordInput.classList.add("invalid");
            isStepValid = false;
        } else {
            clearFieldError(passwordInput);
        }

        if (!passwordConfirmationInput || !passwordConfirmationInput.value) {
            displayFieldError(
                passwordConfirmationInput,
                "La confirmación de la contraseña es obligatoria."
            );
            if (passwordConfirmationInput)
                passwordConfirmationInput.classList.add("invalid");
            isStepValid = false;
        } else if (
            passwordInput &&
            passwordConfirmationInput &&
            passwordInput.value !== passwordConfirmationInput.value
        ) {
            displayFieldError(
                passwordConfirmationInput,
                "La contraseña y la confirmación no coinciden."
            );
            if (passwordInput) passwordInput.classList.add("invalid");
            if (passwordConfirmationInput)
                passwordConfirmationInput.classList.add("invalid");
            isStepValid = false;
        } else {
            clearFieldError(passwordConfirmationInput);
            if (passwordInput) passwordInput.classList.remove("invalid");
        }

        if (emailInput && !emailInput.classList.contains("invalid")) {
            const isEmailUnique = await checkEmailExists(emailInput);
            if (!isEmailUnique) {
                isStepValid = false;
            }
        }

        return isStepValid;
    }

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
            const dniFormatRegex = /^\d{8}[A-Za-z]$/;
            const dniValue = dniInput.value.trim();
            if (!dniFormatRegex.test(dniValue)) {
                displayFieldError(
                    dniInput,
                    "El formato del DNI debe ser 8 números seguidos de una letra."
                );
                if (dniInput) dniInput.classList.add("invalid");
                isStepValid = false;
            } else if (!isValidDniLetter(dniValue)) {
                displayFieldError(
                    dniInput,
                    "La letra del DNI no se corresponde con los números."
                );
                if (dniInput) dniInput.classList.add("invalid");
                isStepValid = false;
            } else {
                clearFieldError(dniInput);
                if (dniInput) dniInput.classList.remove("invalid");

                const isDniUnique = await checkDniExists(dniInput);
                if (!isDniUnique) {
                    isStepValid = false;
                }
            }
        }

        if (!cpInput || !cpInput.value.trim()) {
            displayFieldError(cpInput, "El Código Postal es obligatorio.");
            if (cpInput) cpInput.classList.add("invalid");
            isStepValid = false;
        } else {
            const cpRegex = /^\d{5}$/;
            if (!cpRegex.test(cpInput.value.trim())) {
                displayFieldError(cpInput, "El C.P debe tener 5 dígitos.");
                if (cpInput) cpInput.classList.add("invalid");
                isStepValid = false;
            } else {
                clearFieldError(cpInput);
            }
        }

        return isStepValid;
    }

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

        if (!fechaNacimientoInput || !fechaNacimientoInput.value.trim()) {
            displayFieldError(
                fechaNacimientoInput,
                "La Fecha de Nacimiento es obligatoria."
            );
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
                    displayFieldError(
                        fechaNacimientoInput,
                        "Debes tener al menos 14 años."
                    );
                    fechaNacimientoInput.classList.add("invalid");
                    if (fechaNacimientoInput)
                        fechaNacimientoInput.classList.add("invalid");
                    isStepValid = false;
                } else {
                    clearFieldError(fechaNacimientoInput);
                }
            }
        }

        if (!mayorEdadCheckbox || !mayorEdadCheckbox.checked) {
            if (!mayorEdadCheckbox) {
            } else {
                displayFieldError(
                    mayorEdadCheckbox,
                    "Debes confirmar que eres mayor de 14 años."
                );
                mayorEdadCheckbox.classList.add("invalid");
                isStepValid = false;
            }
        } else {
            clearFieldError(mayorEdadCheckbox);
        }

        return isStepValid;
    }

    function displayFieldError(inputElement, message) {
        const formRow = inputElement.closest(".form-row");
        if (formRow) {
            const errorElement = formRow.querySelector(
                ".client-side-field-error"
            );
            if (errorElement) {
                errorElement.innerHTML = message;
                errorElement.style.display = "block";
            }
        }
    }

    function clearFieldError(inputElement) {
        const formRow = inputElement.closest(".form-row");
        if (formRow) {
            const errorElement = formRow.querySelector(
                ".client-side-field-error"
            );
            if (errorElement) {
                errorElement.innerHTML = "";
                errorElement.style.display = "none";
            }
        }
    }

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

        const errorElement = emailInput
            .closest(".form-row")
            ?.querySelector(".client-side-field-error");

        if (
            errorElement &&
            errorElement.innerHTML.includes("ya está registrado")
        ) {
            clearFieldError(emailInput);
        }
        if (emailInput) emailInput.classList.remove("invalid");

        try {
            const response = await fetch(
                `${checkUrlBase}email?email=${encodeURIComponent(email)}`
            );

            if (!response.ok) {
                console.error(
                    "Error en la respuesta del backend al comprobar email:",
                    response.status,
                    response.statusText
                );
                displayFieldError(
                    emailInput,
                    "Error al verificar email. Intenta de nuevo."
                );
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
            console.error(
                "Error al hacer la petición fetch para comprobar email:",
                error
            );
            displayFieldError(
                emailInput,
                "Error al verificar email. Intenta de nuevo."
            );
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

        const errorElement = dniInput
            .closest(".form-row")
            ?.querySelector(".client-side-field-error");

        if (
            errorElement &&
            errorElement.innerHTML.includes("ya existe ese DNI")
        ) {
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
            const response = await fetch(
                `${checkUrlBase}dni?dni=${encodeURIComponent(dni)}`
            );

            if (!response.ok) {
                console.error(
                    "Error en la respuesta del backend al comprobar DNI:",
                    response.status,
                    response.statusText
                );
                displayFieldError(
                    dniInput,
                    "Error al verificar DNI. Intenta de nuevo."
                );
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
            console.error(
                "Error al hacer la petición fetch para comprobar DNI:",
                error
            );
            displayFieldError(
                dniInput,
                "Error al verificar DNI. Intenta de nuevo."
            );
            if (dniInput) dniInput.classList.add("invalid");
            return false;
        }
    }

    form.addEventListener("submit", async function (event) {
        event.preventDefault();

        hideErrors();
        clearInvalidClasses();

        steps.forEach((step) => clearStepFieldErrors(step));

        const isStep1Valid = await validateStep1();
        const isStep2Valid = await validateStep2();
        const isStep3Valid = await validateStep3();

        const isFormValid = isStep1Valid && isStep2Valid && isStep3Valid;

        if (isFormValid) {
            console.log("Validación final del formulario exitosa. Enviando...");
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

    prevButtons.forEach((button) => {
        button.addEventListener("click", function () {
            if (currentStepIndex > 0) {
                showStep(currentStepIndex - 1);
            }
        });
    });

    // Lógica para detectar errores de servidor al cargar la página y mostrar el modal/paso
    const registrationForm = modalRegistro?.querySelector("form");
    const hasServerErrors =
        registrationForm &&
        registrationForm.querySelector(".error-messages") !== null;

    if (hasServerErrors) {
        console.log(
            "Errores de servidor detectados en el formulario de registro. Abriendo modal y mostrando paso con error."
        );

        if (modalRegistro && modalRegistro.classList.contains("hidden")) {
            openModal();
        } else if (modalRegistro && !modalRegistro.classList.contains("flex")) {
            modalRegistro.classList.add("flex");
            modalRegistro.classList.remove("hidden");
        }

        let stepToShow = 0;
        for (let i = 0; i < steps.length; i++) {
            if (steps[i].querySelector(".error-messages") !== null) {
                stepToShow = i;
                break;
            }
        }

        setTimeout(() => {
            showStep(stepToShow);
            modalRegistro
                .querySelectorAll(".error-messages")
                .forEach((el) => (el.style.display = "block"));
        }, 100);
    } else {
        if (modalRegistro && !modalRegistro.classList.contains("flex")) {
            modalRegistro.classList.add("hidden");
            modalRegistro.classList.remove("flex");
        }
    }


    // Para que el modal registro se cierre al presionar Escape (también se limpia de datos)
    if (modalRegistro) {
        document.addEventListener('keydown', function (event) {
            if ((event.key === 'Escape' || event.keyCode === 27) && modalRegistro.classList.contains('flex')) {
                closeModal();
            }
        });
    }


    // Para que el modal registro se cierre al clicar fuera del modal (también se limpia de datos)
    if (modalRegistro) {
        modalRegistro.addEventListener('click', function (event) {
            if (event.target === modalRegistro && modalRegistro.classList.contains('flex')) {
                closeModal();
            }
        });
    }


});
