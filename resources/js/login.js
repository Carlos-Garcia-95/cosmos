document.addEventListener("DOMContentLoaded", function () {
    const modalLogin = document.getElementById("modalLogin");
    const mostrarLoginBtn = document.getElementById("mostrarLogin");
    const cerrarLoginBtn = modalLogin?.querySelector("#cerrarLogin");
    const volverLoginModalBtn = modalLogin?.querySelector("#volverLoginModal");
    const form = modalLogin?.querySelector("form");
    const clientSideErrorArea = modalLogin?.querySelector('.client-side-errors');

    function showErrorsJS(messages) {
        if (!clientSideErrorArea) return;
        clientSideErrorArea.innerHTML = messages.join('<br>');
        clientSideErrorArea.style.display = 'block';
        modalLogin.querySelectorAll('.error-text').forEach(el => el.style.display = 'none');
    }

    function hideErrorsJS() {
        if (!clientSideErrorArea) return;
        clientSideErrorArea.innerHTML = '';
        clientSideErrorArea.style.display = 'none';
        modalLogin.querySelectorAll('.error-text').forEach(el => el.style.display = 'none');
    }

    function clearInvalidClassesLogin() {
        modalLogin?.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));
    }

    function openLoginModal() {
        if (modalLogin) {
            modalLogin.classList.remove('hidden');
            modalLogin.classList.add('flex');
            document.body.classList.add('modal_abierto');

            modalLogin.querySelectorAll('.error-text').forEach(el => el.style.display = 'block');

            const firstInput = modalLogin.querySelector('input:not([type="hidden"]), select, textarea');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 50);
            }
        }
    }

    function closeLoginModal() {
        if (modalLogin) {
            modalLogin.classList.remove('flex');
            modalLogin.classList.add('hidden');
            document.body.classList.remove('modal_abierto');

            const loginForm = modalLogin.querySelector('form');
            if (loginForm) loginForm.reset();
            modalLogin.querySelectorAll('.error-text').forEach(el => el.style.display = 'none');
            clearClientFieldErrors();
            clearInvalidClassesLogin();
            hideErrorsJS();
        }
    }

    if (mostrarLoginBtn) {
        mostrarLoginBtn.addEventListener("click", openLoginModal);
    }

    if (cerrarLoginBtn) {
        cerrarLoginBtn.addEventListener("click", closeLoginModal);
    }

    if (volverLoginModalBtn) {
        volverLoginModalBtn.addEventListener("click", closeLoginModal);
    }

    const loginForm = modalLogin?.querySelector('form');
    const hasServerErrors = loginForm && loginForm.querySelector('.error-text') !== null;

    if (hasServerErrors) {
        openLoginModal();
    } else {
         if (modalLogin && !modalLogin.classList.contains('flex')) {
            modalLogin.classList.add('hidden');
            modalLogin.classList.remove('flex');
         }
    }

    if (form) {
        form.addEventListener("submit", async function (event) {
            event.preventDefault();

            hideErrorsJS();
            clearInvalidClassesLogin();
            clearClientFieldErrors();

            let isFormValid = true;

            const emailInput = form.querySelector('[name="login_email"]');
            const passwordInput = form.querySelector('[name="login_password"]');

            if (!emailInput || !emailInput.value.trim()) {
                displayFieldError(emailInput, "El email es obligatorio.");
                if (emailInput) emailInput.classList.add("invalid");
                isFormValid = false;
            } else if (!/\S+@\S+\.\S+/.test(emailInput.value)) {
                displayFieldError(emailInput, "Por favor, introduce un email válido.");
                if (emailInput) emailInput.classList.add("invalid");
                isFormValid = false;
            } else {
                clearFieldError(emailInput);
                if (emailInput) emailInput.classList.remove("invalid");
            }

            if (!passwordInput || !passwordInput.value.trim()) {
                displayFieldError(passwordInput, "La contraseña es obligatoria.");
                if (passwordInput) passwordInput.classList.add("invalid");
                isFormValid = false;
            } else {
                clearFieldError(passwordInput);
                if (passwordInput) passwordInput.classList.remove("invalid");
            }

            if (isFormValid) {
                event.target.submit();
            }
        });
    }

    function displayFieldError(inputElement, message) {
        const formRow = inputElement?.closest(".form-row");
        if (formRow) {
            const errorElement = formRow.querySelector(".client-side-field-error");
            if (errorElement) {
                errorElement.innerHTML = message;
                errorElement.style.display = "block";
            }
        }
    }

    function clearFieldError(inputElement) {
        const formRow = inputElement?.closest(".form-row");
        if (formRow) {
            const errorElement = formRow.querySelector(".client-side-field-error");
            if (errorElement) {
                errorElement.innerHTML = "";
                errorElement.style.display = "none";
            }
        }
    }

    function clearClientFieldErrors() {
        modalLogin.querySelectorAll('.client-side-field-error').forEach(el => {
            el.innerHTML = "";
            el.style.display = "none";
        });
    }

    // --- Funciones para Google Sign-In ---

    function handleCredentialResponse(response) {
        console.log("ID Token recibido: " + response.credential);
        sendTokenToBackend(response.credential);
    }

    function sendTokenToBackend(token) {
        fetch('/api/auth/google', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ token: token })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del backend:', data);
            if (data.success) {
                window.location.href = '/pagina-de-inicio-logueado';
            } else {
                alert('Error al iniciar sesión con Google en el backend.');
            }
        })
        .catch((error) => {
            console.error('Error enviando token al backend:', error);
            alert('Ocurrió un error durante el inicio de sesión.');
        });
    }

    // Para que el modal login se cierre al presionar Escape (también se limpia de datos)
    if (modalLogin) {
        document.addEventListener('keydown', function (event) {
            if ((event.key === 'Escape' || event.keyCode === 27) && modalLogin.classList.contains('flex')) {
                closeLoginModal();
            }
        });
    }


    // Para que el modal login se cierre al clicar fuera del modal (también se limpia de datos)
    if (modalLogin) {
        modalLogin.addEventListener('click', function (event) {
            if (event.target === modalLogin && modalLogin.classList.contains('flex')) {
                closeLoginModal();
            }
        });
    }
    

    // Asegúrate de que la función handleCredentialResponse esté globalmente accesible
    // o accesible en el scope donde Google la buscará.
    // Si usas un módulo, podrías necesitar asignarla a window.
    // window.handleCredentialResponse = handleCredentialResponse;

});



