import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { Draggable } from "gsap/Draggable";

gsap.registerPlugin(ScrollTrigger, Draggable);

document.addEventListener("DOMContentLoaded", function () {
    // Referencias a los elementos del DOM
    // Suponiendo que E es la instancia del slider, por ejemplo:
    const sliderContainer = document.querySelector('[data-slider="list"]');
    const slides = gsap.utils.toArray('[data-slider="slide"]');
    const prevBtn = document.querySelector('[data-slider="button-prev"]');
    const nextBtn = document.querySelector('[data-slider="button-next"]');

    let currentIndex = 0;
    const slideCount = slides.length;

    // Función para actualizar el slider
    const updateSlider = (index) => {
        currentIndex = gsap.utils.wrap(0, slideCount, index); // Aseguramos que el índice sea infinito
        gsap.to(sliderContainer, {
            xPercent: -100 * currentIndex,
            duration: 0.725,
            ease: "power3.out",
        });
    };

    // Función para ir al siguiente slide
    nextBtn.addEventListener("click", () => {
        updateSlider(currentIndex + 1); // Ir al siguiente slide
    });

    // Función para ir al slide anterior
    prevBtn.addEventListener("click", () => {
        updateSlider(currentIndex - 1); // Ir al slide anterior
    });

    // Asignar los event listeners a los botones
    nextBtn.addEventListener("click", goNext);
    prevBtn.addEventListener("click", goPrev);

    // Funcionalidad de ScrollTrigger (mover el slider con el scroll)
    ScrollTrigger.create({
        trigger: sliderContainer,
        start: "top top",
        end: "+=2000",
        scrub: true,
        pin: true,
        onUpdate: (self) => {
            const progress = self.progress * slideCount;
            updateSlider(Math.floor(progress));
        },
    });

    // Habilitar el arrastre (dragging) con GSAP Draggable
    Draggable.create(sliderContainer, {
        type: "x",
        bounds: {
            minX: -sliderContainer.offsetWidth + window.innerWidth,
            maxX: 0,
        },
        onDrag() {
            const progress = (this.x / window.innerWidth) * slideCount;
            updateSlider(Math.floor(progress));
        },
        onRelease() {
            const progress = (this.x / window.innerWidth) * slideCount;
            updateSlider(Math.floor(progress));
        },
    });
});

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
