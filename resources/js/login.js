document.addEventListener("DOMContentLoaded", function () {
    const modalLogin = document.getElementById("modalLogin");
    const mostrarLoginBtn = document.getElementById("mostrarLogin");
    const cerrarLoginBtn = document.getElementById("cerrarLogin");
    const steps = modalLogin.querySelectorAll(".form-step");
    const form = modalLogin.querySelector("form");
    let currentStep = 0;

    /* function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.toggle("active", index === stepIndex);
        });
        currentStep = stepIndex;
    } */

    mostrarLoginBtn.addEventListener("click", () => {
        modalLogin.classList.remove('hidden');
        modalLogin.classList.add('flex');
        showStep(0); 
    });

    cerrarLoginBtn.addEventListener("click", () => {
        modalLogin.classList.remove('flex');
        modalLogin.classList.add('hidden');
    });
    
    /* form.addEventListener("submit", function (event) {
        // Aquí puedes agregar validaciones adicionales en JavaScript si lo deseas
        // La submission del formulario seguirá enviando los datos a la ruta 'login'
    }); */

    // Ocultar todos los pasos excepto el primero al cargar el modal
    /* steps.forEach((step, index) => {
        if (index !== 0) {
            step.classList.remove("active");
        }
    }); */

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
});