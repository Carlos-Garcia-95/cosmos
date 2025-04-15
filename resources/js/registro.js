document.addEventListener("DOMContentLoaded", function () {
    const modalRegistro = document.getElementById("modalRegistro");
    const mostrarRegistroBtn = document.getElementById("mostrarRegistro");
    const cerrarRegistroBtn = document.getElementById("cerrarRegistro");
    const steps = modalRegistro.querySelectorAll(".form-step");
    const nextButtons = modalRegistro.querySelectorAll(".next-step");
    const prevButtons = modalRegistro.querySelectorAll(".prev-step");
    const form = modalRegistro.querySelector("form");
    let currentStep = 0;
    
    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.toggle("active", index === stepIndex);
        });
        currentStep = stepIndex;
    }

    mostrarRegistroBtn.addEventListener("click", () => {
        modalRegistro.style.display = "flex";
        showStep(0); // Mostrar el primer paso al abrir el modal
    });

    cerrarRegistroBtn.addEventListener("click", () => {
        modalRegistro.style.display = "none";
    });

    nextButtons.forEach((button) => {
        button.addEventListener("click", () => {
            if (currentStep < steps.length - 1) {
                showStep(currentStep + 1);
            }
        });
    });

    prevButtons.forEach((button) => {
        button.addEventListener("click", () => {
            if (currentStep > 0) {
                showStep(currentStep - 1);
            }
        });
    });

    form.addEventListener("submit", function (event) {
        // Aquí puedes agregar validaciones adicionales en JavaScript si lo deseas
        // La submission del formulario seguirá enviando los datos a la ruta 'registro'
    });

    // Ocultar todos los pasos excepto el primero al cargar el modal
    steps.forEach((step, index) => {
        if (index !== 0) {
            step.classList.remove("active");
        }
    });

    // Ocultar el modal después del envío exitoso
    form.addEventListener("submit", function () {
        // Simulación de envío exitoso
        setTimeout(() => {
            modalRegistro.style.display = "none";
            alert("Registro completado!"); 
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