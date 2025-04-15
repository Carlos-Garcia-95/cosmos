import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { Draggable } from "gsap/Draggable";

gsap.registerPlugin(ScrollTrigger, Draggable);

document.addEventListener("DOMContentLoaded", function () {
    // Referencias a los elementos del DOM
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


