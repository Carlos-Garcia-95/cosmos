document.addEventListener("DOMContentLoaded", function () {
    const modalRegistro = document.getElementById("modalRegistro");
    const mostrarRegistroBtn = document.getElementById("mostrarRegistro");
    const cerrarRegistroBtn = document.getElementById("cerrarRegistro");
    const steps = modalRegistro.querySelectorAll(".form-step");
    const nextButtons = modalRegistro.querySelectorAll(".next-step");
    const prevButtons = modalRegistro.querySelectorAll(".prev-step");
    const form = modalRegistro.querySelector("form");
    let currentStep = 0;

    // Selección de campos
    const emailField = document.querySelector('input[name="email"]');
    const emailConfirmField = document.querySelector('input[name="email_confirmation"]');
    const passwordField = document.querySelector('input[name="password"]');
    const passwordConfirmField = document.querySelector('input[name="password_confirmation"]');
    const errorMessages = document.querySelector('.error-messages');  // Contenedor para mostrar errores

    // Función para mostrar el mensaje de error
    function showError(message) {
        if (!errorMessages) return;
        errorMessages.innerHTML = `<div class="error-message">${message}</div>`;
        errorMessages.style.display = "block"; // Mostrar los mensajes de error
    }

    // Función para ocultar los mensajes de error
    function hideError() {
        if (!errorMessages) return;
        errorMessages.style.display = "none";  // Ocultar los mensajes de error
    }

    // Función para validar los campos de email
    function validateEmailConfirmation() {
        if (emailField.value !== emailConfirmField.value) {
            return 'Los correos electrónicos no coinciden.';
        }
        return '';
    }

    // Función para validar las contraseñas
    function validatePasswordConfirmation() {
        if (passwordField.value !== passwordConfirmField.value) {
            return 'Las contraseñas no coinciden.';
        }
        return '';
    }

    // Función para verificar todos los campos requeridos
    function validateStep(stepElement) {
        const inputs = stepElement.querySelectorAll('input[required], select[required]');
        let valid = true;
        let errorMessages = [];

        inputs.forEach(input => {
            if ((input.type === 'checkbox' && !input.checked) ||
                (input.type !== 'checkbox' && !input.value.trim())) {
                valid = false;
                input.classList.add('invalid');
                errorMessages.push(`${input.name} no está correctamente completado.`);
            } else {
                input.classList.remove('invalid');
            }
        });

        if (errorMessages.length > 0) {
            showError(errorMessages.join('<br>'));
        }

        return valid;
    }

    // Mostrar el modal y resetear al primer paso
    mostrarRegistroBtn?.addEventListener("click", () => {
        modalRegistro.style.display = "flex";
        showStep(0);
    });

    // Cerrar el modal
    cerrarRegistroBtn?.addEventListener("click", () => {
        modalRegistro.style.display = "none";
    });

    // Mostrar el paso
    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.toggle("active", index === stepIndex);
        });
        currentStep = stepIndex;
        hideError();  // Ocultar los mensajes de error al cambiar de paso
    }

    // Cuando se presiona el botón siguiente, validamos
    nextButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const currentFormStep = steps[currentStep];
            let valid = true;

            if (currentStep === 0) {
                // Validación de correo y contraseña solo en el primer paso
                const emailError = validateEmailConfirmation();
                const passwordError = validatePasswordConfirmation();

                if (emailError || passwordError) {
                    showError(emailError || passwordError);
                    valid = false;
                }
            }

            // Validar el paso actual
            if (valid && validateStep(currentFormStep)) {
                if (currentStep < steps.length - 1) {
                    showStep(currentStep + 1);
                }
            } else {
                // No pasar al siguiente paso si no es válido
                alert("Por favor, completa todos los campos requeridos correctamente.");
            }
        });
    });

    // Validación en el paso final
    form.addEventListener("submit", function (event) {
        const lastStep = steps[steps.length - 1];
        if (!validateStep(lastStep)) {
            event.preventDefault();
            alert("Completa todos los campos del paso final.");
        } else {
            // Simulación de envío exitoso
            setTimeout(() => {
                modalRegistro.style.display = "none";
                form.reset();
                showStep(0);
            }, 1500);
        }
    });

    // Mostrar el primer paso desde el inicio
    showStep(0);
});
