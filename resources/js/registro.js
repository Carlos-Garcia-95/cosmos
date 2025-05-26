document.addEventListener("DOMContentLoaded", function () {
    const modalRegistro = document.getElementById("modalRegistro");
    if (!modalRegistro) return;

    const checkUrlBase = "/check-";
    const passwordContainers = modalRegistro.querySelectorAll(".password-input-container");
    const mostrarRegistroBtn = document.getElementById("mostrarRegistro");
    const cerrarRegistroBtn = modalRegistro.querySelector("#cerrarRegistro");
    const form = modalRegistro.querySelector("form");
    const activeStep = modalRegistro.querySelector(".form-step.active");
    const clientSideErrorList = modalRegistro.querySelector(".client-side-errors ul");
    const clientSideErrorContainer = clientSideErrorList?.closest(".client-side-errors");


    passwordContainers.forEach((container) => {
        const passwordInput = container.querySelector('input[type="password"], input[type="text"]');
        const toggleIcon = container.querySelector(".toggle-password");

        if (passwordInput && toggleIcon) {
            toggleIcon.addEventListener("click", function () {
                const currentType = passwordInput.getAttribute("type");
                const newType = currentType === "password" ? "text" : "password";
                passwordInput.setAttribute("type", newType);
                toggleIcon.textContent = newType === "password" ? "👁️" : "🙈";
            });
        }
    });

    function openModal() {
        modalRegistro.classList.remove("hidden");
        modalRegistro.classList.add("flex");
        document.body.classList.add('modal_abierto');
        const firstInput = activeStep?.querySelector('input:not([type="hidden"]), select, textarea');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 50);
        }
    }

    function closeModal() {
        modalRegistro.classList.remove("flex");
        modalRegistro.classList.add("hidden");
        document.body.classList.remove('modal_abierto');
        resetForm();
        modalRegistro.querySelectorAll(".error-message.backend-error").forEach(el => {
            if (el.textContent.trim() !== '') {
                 el.style.display = "none"; // Ocultar solo si se mostró por error de backend
            }
        });
    }

    if (cerrarRegistroBtn) cerrarRegistroBtn.addEventListener("click", closeModal);
    if (mostrarRegistroBtn) mostrarRegistroBtn.addEventListener("click", openModal);

    function showGeneralClientErrors(messages) {
        if (!clientSideErrorList || !clientSideErrorContainer) return;
        clientSideErrorList.innerHTML = '';
        messages.forEach(msg => {
            const li = document.createElement('li');
            li.textContent = msg;
            clientSideErrorList.appendChild(li);
        });
        clientSideErrorContainer.style.display = messages.length > 0 ? "block" : "none";
    }

    function hideGeneralClientErrors() {
        if (!clientSideErrorList || !clientSideErrorContainer) return;
        clientSideErrorList.innerHTML = "";
        clientSideErrorContainer.style.display = "none";
    }

    function clearInvalidClassesFromInputs() {
        activeStep?.querySelectorAll("input.invalid, select.invalid").forEach(el => el.classList.remove("invalid"));
    }

    function resetForm() {
        if (form) form.reset();
        hideGeneralClientErrors();
        if (activeStep) clearStepFieldErrors(activeStep);
        clearInvalidClassesFromInputs();
        modalRegistro.querySelectorAll(".error-message.backend-error").forEach(el => el.style.display = "none");
    }

    function displayFieldError(inputElement, message) {
        if (!inputElement) return;
        const formRow = inputElement.closest(".form-row");
        if (formRow) {
            const errorElement = formRow.querySelector(".client-side-field-error");
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = "block";
            }
            inputElement.classList.add("invalid");
        }
    }

    function clearFieldError(inputElement) {
        if (!inputElement) return;
        const formRow = inputElement.closest(".form-row");
        if (formRow) {
            const errorElement = formRow.querySelector(".client-side-field-error");
            if (errorElement) {
                errorElement.textContent = "";
                errorElement.style.display = "none";
            }
            inputElement.classList.remove("invalid");
        }
    }

    function clearStepFieldErrors(stepElement) {
        if (stepElement && typeof stepElement.querySelectorAll === 'function') {
            stepElement.querySelectorAll(".form-row .client-side-field-error").forEach((el) => {
                el.textContent = "";
                el.style.display = "none";
            });
        } else {
            console.warn("clearStepFieldErrors: stepElement inválido:", stepElement);
        }
    }

    async function checkEmailExists(emailInput) {
        if (!emailInput || !emailInput.value.trim()) {
            clearFieldError(emailInput);
            return true; // Si está vacío, no está "ocupado", la validación de 'required' se encarga
        }

        const email = emailInput.value.trim();
        try {
            const response = await fetch(`${checkUrlBase}email?email=${encodeURIComponent(email)}`);
            if (!response.ok) {
                displayFieldError(emailInput, "Error al verificar email. Intenta de nuevo.");
                return false;
            }
            const data = await response.json();
            if (data.exists) {
                displayFieldError(emailInput, "Este email ya está registrado.");
                return false;
            } else {
                clearFieldError(emailInput); // Limpiar si NO existe y previamente había error
                return true;
            }
        } catch (error) {
            displayFieldError(emailInput, "Error de conexión al verificar email.");
            return false;
        }
    }

    async function validateRegistroForm() {
        hideGeneralClientErrors();
        clearInvalidClassesFromInputs();
        if (activeStep) clearStepFieldErrors(activeStep);

        const emailInput = activeStep?.querySelector('[name="email"]');
        const emailConfirmationInput = activeStep?.querySelector('[name="email_confirmation"]');
        const passwordInput = activeStep?.querySelector('[name="password"]');
        const passwordConfirmationInput = activeStep?.querySelector('[name="password_confirmation"]');
        let isFormValid = true;

        if (!emailInput || !emailInput.value.trim()) {
            displayFieldError(emailInput, "El email es obligatorio.");
            isFormValid = false;
        } else if (!/\S+@\S+\.\S+/.test(emailInput.value)) {
            displayFieldError(emailInput, "Por favor, introduce un email válido.");
            isFormValid = false;
        } else {
            clearFieldError(emailInput);
        }

        if (!emailConfirmationInput || !emailConfirmationInput.value.trim()) {
            displayFieldError(emailConfirmationInput, "La confirmación del email es obligatoria.");
            isFormValid = false;
        } else if (emailInput && emailInput.value.trim() !== emailConfirmationInput.value.trim()) {
            displayFieldError(emailConfirmationInput, "El email y la confirmación no coinciden.");
            isFormValid = false;
        } else {
            clearFieldError(emailConfirmationInput);
        }

        if (!passwordInput || !passwordInput.value) {
            displayFieldError(passwordInput, "La contraseña es obligatoria.");
            isFormValid = false;
        } else if (passwordInput.value.length < 8) {
            displayFieldError(passwordInput, "La contraseña debe tener al menos 8 caracteres.");
            isFormValid = false;
        } else {
            clearFieldError(passwordInput);
        }

        if (!passwordConfirmationInput || !passwordConfirmationInput.value) {
            displayFieldError(passwordConfirmationInput, "La confirmación de la contraseña es obligatoria.");
            isFormValid = false;
        } else if (passwordInput && passwordInput.value !== passwordConfirmationInput.value) {
            displayFieldError(passwordConfirmationInput, "La contraseña y la confirmación no coinciden.");
            isFormValid = false;
        } else {
            clearFieldError(passwordConfirmationInput);
        }

        if (emailInput && emailInput.value.trim() && /\S+@\S+\.\S+/.test(emailInput.value) && isFormValid) {
            const isEmailUnique = await checkEmailExists(emailInput);
            if (!isEmailUnique) {
                isFormValid = false;
            }
        }
        
        const recaptchaResponse = typeof grecaptcha !== 'undefined' ? grecaptcha.getResponse() : '';
        if (typeof grecaptcha !== 'undefined' && !recaptchaResponse) {
            showGeneralClientErrors(["Por favor, completa el reCAPTCHA."]);
            isFormValid = false;
        }

        return isFormValid;
    }

    if (form) {
        form.addEventListener("submit", async function (event) {
            event.preventDefault();
            hideGeneralClientErrors();
            clearInvalidClassesFromInputs();
            if (activeStep) clearStepFieldErrors(activeStep);

            const isFormValid = await validateRegistroForm();

            if (isFormValid) {
                event.target.submit();
            } else {
                console.log("Validación del cliente (registro) fallida.");
            }
        });
    }

    const backendErrorMessages = modalRegistro?.querySelectorAll("form .error-message.backend-error");
    let hasServerErrorsOnLoad = false;
    backendErrorMessages?.forEach(el => {
        if (el.textContent.trim() !== '') {
            hasServerErrorsOnLoad = true;
            el.style.display = "block"; // Asegurar que se muestren si tienen contenido
        }
    });

    if (hasServerErrorsOnLoad) {
        if (modalRegistro && (modalRegistro.classList.contains("hidden") || !modalRegistro.classList.contains("flex"))) {
            openModal();
        }
    } else {
        if (modalRegistro && !modalRegistro.classList.contains("flex") && !modalRegistro.classList.contains("hidden")) {
             modalRegistro.classList.add("hidden"); // Asegurar que esté oculto si no hay errores al cargar
        } else if (modalRegistro && modalRegistro.classList.contains("flex")) {
            // Si está flex pero no debería, ocultarlo.
            // Esto es más para el estado inicial antes de cualquier interacción.
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
