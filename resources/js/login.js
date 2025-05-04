/* document.addEventListener("DOMContentLoaded", function () {
    const modalLogin = document.getElementById("modalLogin");
    const mostrarLoginBtn = document.getElementById("mostrarLogin");
    const cerrarLoginBtn = document.getElementById("cerrarLogin");
    const steps = modalLogin.querySelectorAll(".form-step");
    const form = modalLogin.querySelector("form");
    let currentStep = 0;

    mostrarLoginBtn.addEventListener("click", () => {
        modalLogin.classList.remove('hidden');
        modalLogin.classList.add('flex');
        showStep(0); 
    });

    cerrarLoginBtn.addEventListener("click", () => {
        modalLogin.classList.remove('flex');
        modalLogin.classList.add('hidden');
    });

    // Ocultar el modal después del envío exitoso
    form.addEventListener("submit", function () {
        // Simulación de envío exitoso
        setTimeout(() => {
            modalLogin.style.display = "none";
            //alert("Login completado!"); 
            form.reset(); 
            showStep(0); 
        }, 1500); 
    });

    // Ocultar todos los pasos excepto el primero al cargar la página
    steps.forEach((step, index) => {
        if (index !== 0) {
            step.classList.remove("active");
        }
    });
}); */


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
    }

    function hideErrorsJS() {
        if (!clientSideErrorArea) return;
        clientSideErrorArea.innerHTML = '';
        clientSideErrorArea.style.display = 'none';
    }

    
    function clearInvalidClassesLogin() {
        modalLogin?.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));
    }


    // Funciones para abrir y cerrar el modal de LOGIN
    function openLoginModal() {
        if (modalLogin) {

            modalLogin.classList.remove('hidden');
            modalLogin.classList.add('flex');

            const loginForm = modalLogin.querySelector('form');
            const loginLaravelErrorsExist = loginForm && loginForm.querySelector('.error-message') !== null;

            if (!loginLaravelErrorsExist) {
                
                if (loginForm) loginForm.reset();
                hideErrorsJS();
                clearInvalidClassesLogin();
                modalLogin.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
            } else {
                hideErrorsJS();
                clearInvalidClassesLogin();
            }


            //Enfocar el primer campo del formulario al abrir
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

            const loginForm = modalLogin.querySelector('form');
            if (loginForm) loginForm.reset();
            hideErrorsJS();
            clearInvalidClassesLogin();
            modalLogin.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
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


    const loginModalHasLaravelErrors = modalLogin && (modalLogin.classList.contains('flex'));

    if (loginModalHasLaravelErrors) {
        console.log("Modal de Login visible al cargar debido a errores de Laravel.")
        hideErrorsJS();
        clearInvalidClassesLogin();
    } else {
        if (modalLogin) {
            modalLogin.classList.add('hidden');
            modalLogin.classList.remove('flex');
        }
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


});
