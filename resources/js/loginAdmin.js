document.addEventListener('DOMContentLoaded', () => {


    // --- Lógica de Manejo Dinámico de Errores en el Formulario de Login de Administrador ---
    const adminLoginForm = document.getElementById('adminLoginForm');

    if (adminLoginForm) {
        const inputs = adminLoginForm.querySelectorAll('input[name], select[name], textarea[name]');

        inputs.forEach(input => {
            input.addEventListener('input', () => {
                const errorMessageElement = input.nextElementSibling;

                if (errorMessageElement && errorMessageElement.classList.contains('error-message')) {
                    errorMessageElement.style.display = 'none';
                }

                input.classList.remove('is-invalid');
            });
        });
    }

        const togglePassword = document.querySelector('.login-form .toggle-password');
        const passwordInput = document.querySelector('.login-form input[name="password"]');
    
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', () => {
                const currentType = passwordInput.getAttribute('type');
    
                const newType = currentType === 'password' ? 'text' : 'password';
                const newEmoji = newType === 'password' ? '👁️' : '🙈';
    
                passwordInput.setAttribute('type', newType);
                togglePassword.textContent = newEmoji;
            });
        }

});