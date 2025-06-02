document.addEventListener("DOMContentLoaded", function () {
    const modalLogin = document.getElementById("modalLogin");

    if (!modalLogin) return;

    const mostrarLoginBtn = document.getElementById("mostrarLogin");
    const cerrarLoginBtnModalLogin = modalLogin?.querySelector("#cerrarLogin");
    const volverLoginModalBtn = modalLogin?.querySelector("#volverLoginModal");
    const formLogin = modalLogin?.querySelector("form");
    const clientSideLoginErrorArea = modalLogin?.querySelector('.client-side-errors');
    const loginPasswordContainers = modalLogin?.querySelectorAll('.password-input-container');

    // --- Funcionalidad de mostrar/ocultar contraseña (Login) ---
    document.querySelectorAll('.password-input-container').forEach((container) => {
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

    // --- Funciones de utilidad para errores (Login) ---
    function showErrorsJS(messages) {
        if (!clientSideLoginErrorArea) return;
        clientSideLoginErrorArea.innerHTML = messages.join('<br>');
        clientSideLoginErrorArea.style.display = 'block';
        modalLogin?.querySelectorAll('.error-text').forEach(el => el.style.display = 'none');
    }

    function hideErrorsJS() {
        if (!clientSideLoginErrorArea) return;
        clientSideLoginErrorArea.innerHTML = '';
        clientSideLoginErrorArea.style.display = 'none';
        modalLogin?.querySelectorAll('.error-text').forEach(el => el.style.display = 'none');
    }

    function clearInvalidClassesLogin() {
        modalLogin?.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));
    }

    function displayFieldErrorLogin(inputElement, message) {
        const formRow = inputElement?.closest(".form-row");
        if (formRow) {
            const errorElement = formRow.querySelector(".client-side-field-error");
            if (errorElement) {
                errorElement.innerHTML = message;
                errorElement.style.display = "block";
            }
        }
    }

    function clearFieldErrorLogin(inputElement) {
        const formRow = inputElement?.closest(".form-row");
        if (formRow) {
            const errorElement = formRow.querySelector(".client-side-field-error");
            if (errorElement) {
                errorElement.innerHTML = "";
                errorElement.style.display = "none";
            }
        }
    }

    function clearClientFieldErrorsLogin() {
        modalLogin?.querySelectorAll('.client-side-field-error').forEach(el => {
            el.innerHTML = "";
            el.style.display = "none";
        });
    }

    // --- Event listeners y lógica para el modal de login ---
    if (mostrarLoginBtn) mostrarLoginBtn.addEventListener("click", () => openModal(modalLogin));
    if (cerrarLoginBtnModalLogin) cerrarLoginBtnModalLogin.addEventListener("click", () => closeModal(modalLogin, true));
    if (volverLoginModalBtn) volverLoginModalBtn.addEventListener("click", () => closeModal(modalLogin, true));

    if (formLogin) {
        formLogin.addEventListener("submit", async function (event) {
            console.log('Submit del formulario de login detectado.');
            event.preventDefault();

            hideErrorsJS();
            clearInvalidClassesLogin();
            clearClientFieldErrorsLogin();

            let isFormValid = true;

            const emailInput = formLogin.querySelector('[name="login_email"]');
            const passwordInput = formLogin.querySelector('[name="login_password"]');

            console.log('Elemento emailInput:', emailInput);
            console.log('Elemento passwordInput:', passwordInput);

            console.log('Valor del email:', emailInput?.value);
            console.log('Valor de la contraseña:', passwordInput?.value);

            if (!emailInput || !emailInput.value?.trim()) {
                console.log('Error: Email está vacío.');
                displayFieldErrorLogin(emailInput, "El email es obligatorio.");
                if (emailInput) emailInput.classList.add("invalid");
                isFormValid = false;
            } else if (!/\S+@\S+\.\S+/.test(emailInput.value)) {
                console.log('Error: Email no válido:', emailInput.value);
                displayFieldErrorLogin(emailInput, "Por favor, introduce un email válido.");
                if (emailInput) emailInput.classList.add("invalid");
                isFormValid = false;
            } else {
                clearFieldErrorLogin(emailInput);
                if (emailInput) emailInput.classList.remove("invalid");
            }

            if (!passwordInput || !passwordInput.value?.trim()) {
                console.log('Error: Contraseña está vacía.');
                displayFieldErrorLogin(passwordInput, "La contraseña es obligatoria.");
                if (passwordInput) passwordInput.classList.add("invalid");
                isFormValid = false;
            } else {
                clearFieldErrorLogin(passwordInput);
                if (passwordInput) passwordInput.classList.remove("invalid");
            }

            // --- Validación del reCAPTCHA para Login ---
            const recaptchaContainer = modalLogin?.querySelector('.g-recaptcha .login-recap');
            let recaptchaResponse = '';
            if (typeof grecaptcha !== 'undefined' && recaptchaContainer) {
                recaptchaResponse = grecaptcha.getResponse();
                if (!recaptchaResponse) {
                    // Intenta obtener la respuesta de nuevo (podría ser un retraso)
                    recaptchaResponse = grecaptcha.getResponse();
                }
            } else {
                
            }

            if (recaptchaContainer && typeof grecaptcha !== 'undefined' && !recaptchaResponse) {
                console.log("Error de reCAPTCHA detectado.");
                showErrorsJS(["Por favor, completa el reCAPTCHA."]);
                isFormValid = false;
            } else {
                console.log("reCAPTCHA OK o no presente.");
            }

            if (isFormValid) {
                event.target.submit();
            }
        });
    }

    // --- Funciones para abrir y cerrar modales ---
    function openModal(modal) {
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('modal_abierto');
            const firstInput = modal.querySelector('input:not([type="hidden"]), select, textarea');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 50);
            }
            console.log('Modal de login abierto.');
        }
    }

    function closeModal(modal, reset = false) {
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.classList.remove('modal_abierto');
            if (reset && modal.querySelector('form')) {
                modal.querySelector('form').reset();
                const errorContainers = modal.querySelectorAll('.error-text, .client-side-field-error');
                errorContainers.forEach(el => el.style.display = 'none');
                hideErrorsJS();
                clearInvalidClassesLogin();
                clearClientFieldErrorsLogin();
            }
        }
    }

    // --- Lógica para mostrar modales si hay errores del servidor al cargar la página ---
    const hasLoginServerErrors = modalLogin?.querySelector("form .error-text") !== null;
    if (hasLoginServerErrors) {
        openModal(modalLogin);
    } else if (modalLogin && !modalLogin.classList.contains('flex')) {
        modalLogin.classList.add('hidden');
    }

    // --- Cerrar modales con la tecla Escape y al hacer clic fuera ---
    document.addEventListener('keydown', function (event) {
        if ((event.key === 'Escape' || event.keyCode === 27)) {
            if (modalLogin?.classList.contains('flex')) closeModal(modalLogin, true);
        }
    });

    document.addEventListener('click', function (event) {
        if (event.target === modalLogin && modalLogin.classList.contains('flex')) {
            closeModal(modalLogin, true);
        }
    });
});