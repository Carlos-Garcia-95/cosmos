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

    let registroRecaptchaWidgetId = null;

    function renderRegistroRecaptcha() {
        const container = document.getElementById('recaptcha-registro-container');
        if (container && typeof grecaptcha !== 'undefined' && grecaptcha.render) {
            if (registroRecaptchaWidgetId === null) {
                try {
                    registroRecaptchaWidgetId = grecaptcha.render('recaptcha-registro-container', {
                        'sitekey': container.dataset.sitekey
                    });
                } catch (e) {
                    console.error("Error render reCAPTCHA registro:", e);
                }
            } else {
                grecaptcha.reset(registroRecaptchaWidgetId);
            }
        }
    }

    passwordContainers.forEach((container) => {
        const passwordInput = container.querySelector('input[type="password"], input[type="text"]');
        const toggleIcon = container.querySelector(".toggle-password"); // Asegúrate que este selector es correcto, en tu HTML original era .toggle-password1

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
        renderRegistroRecaptcha();
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
        if (typeof grecaptcha !== 'undefined' && grecaptcha.reset && registroRecaptchaWidgetId !== null) {
            grecaptcha.reset(registroRecaptchaWidgetId);
        }
        modalRegistro.querySelectorAll(".error-messages").forEach(el => {
            if (el.textContent.trim() !== '') {
                 el.style.display = "none";
            }
        });
    }

    if (cerrarRegistroBtn) cerrarRegistroBtn.addEventListener("click", closeModal);
    if (mostrarRegistroBtn) mostrarRegistroBtn.addEventListener("click", openModal);

    const cancelarRegistroBtn = modalRegistro.querySelector("#cancelarRegistroBtn");
    if (cancelarRegistroBtn) {
        cancelarRegistroBtn.addEventListener('click', closeModal);
    }

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
        modalRegistro.querySelectorAll(".error-messages").forEach(el => el.style.display = "none");
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
        }
    }

    async function checkEmailExists(emailInput) {
        if (!emailInput || !emailInput.value.trim()) {
            clearFieldError(emailInput);
            return true;
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
                clearFieldError(emailInput);
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

        const recaptchaRegistroContainer = document.getElementById('recaptcha-registro-container');
        let recaptchaResponseRegistro = '';
        if (typeof grecaptcha !== 'undefined' && registroRecaptchaWidgetId !== null) {
            recaptchaResponseRegistro = grecaptcha.getResponse(registroRecaptchaWidgetId);
        }

        if (recaptchaRegistroContainer && typeof grecaptcha !== 'undefined' && registroRecaptchaWidgetId !== null && recaptchaResponseRegistro.length === 0) {
            showGeneralClientErrors(["Por favor, completa el reCAPTCHA."]);
            isFormValid = false;
        } else if (recaptchaRegistroContainer && (registroRecaptchaWidgetId === null || typeof grecaptcha === 'undefined')) {
             showGeneralClientErrors(["Hubo un problema con el reCAPTCHA. Inténtalo de nuevo."]);
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
            }
        });
    }

    const backendErrorMessages = modalRegistro?.querySelectorAll("form .error-messages, form .error-text"); // Incluir .error-text si también se usa
    let hasServerErrorsOnLoad = false;
    backendErrorMessages?.forEach(el => {
        if (el.textContent.trim() !== '') {
            hasServerErrorsOnLoad = true;
            el.style.display = "block";
        }
    });

    if (hasServerErrorsOnLoad) {
        if (modalRegistro && (modalRegistro.classList.contains("hidden") || !modalRegistro.classList.contains("flex"))) {
            openModal();
        }
    }

    if (modalRegistro) {
        document.addEventListener('keydown', function (event) {
            if ((event.key === 'Escape' || event.keyCode === 27) && modalRegistro.classList.contains('flex')) {
                closeModal();
            }
        });
        modalRegistro.addEventListener('click', function (event) {
            if (event.target === modalRegistro && modalRegistro.classList.contains('flex')) {
                closeModal();
            }
        });
    }
});