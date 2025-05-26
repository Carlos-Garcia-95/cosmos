import Swiper from 'swiper';
// Módulos básicos para empezar: Navigation y Pagination. Luego podemos añadir Autoplay, EffectFade.
import { Navigation, Pagination } from 'swiper/modules';

// Importar los estilos base de Swiper y módulos que SÍ estamos usando
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
// import 'swiper/css/effect-fade'; // Solo si vas a usar EffectFade y lo configuras

document.addEventListener('DOMContentLoaded', function () {
    console.log("[HeroSwiper] DOMContentLoaded disparado.");

    const heroSwiperElement = document.querySelector('.hero-movie-swiper');
    console.log("[HeroSwiper] heroSwiperElement:", heroSwiperElement);

    // --- INICIO DE AJUSTE PARA EL JSON ---
    let rawPeliculasDataFromWindow = window.heroSliderPeliculasData;
    let peliculasData; // Esta será la variable que usaremos, asegurándonos de que sea un array

    if (Array.isArray(rawPeliculasDataFromWindow)) {
        console.log("[HeroSwiper] window.heroSliderPeliculasData ya es un Array.");
        peliculasData = rawPeliculasDataFromWindow;
    } else if (typeof rawPeliculasDataFromWindow === 'object' && rawPeliculasDataFromWindow !== null) {
        console.log("[HeroSwiper] window.heroSliderPeliculasData es un Objeto. Intentando convertir a Array usando Object.values().");
        peliculasData = Object.values(rawPeliculasDataFromWindow);
        // Verificar si la conversión fue exitosa y si los elementos son lo que esperamos (objetos)
        if (peliculasData.length > 0 && typeof peliculasData[0] !== 'object') {
            console.warn("[HeroSwiper] La conversión con Object.values() no resultó en un array de objetos. Revisar la estructura de window.heroSliderPeliculasData.");
            // Podrías querer revertir a un array vacío si la conversión no es la esperada
            // peliculasData = [];
        }
    } else {
        console.warn("[HeroSwiper] window.heroSliderPeliculasData no es ni Array ni Objeto, o es null. Se usará un array vacío.");
        peliculasData = [];
    }
    // --- FIN DE AJUSTE PARA EL JSON ---

    console.log("[HeroSwiper] peliculasData (después del posible ajuste):", peliculasData);
    console.log("[HeroSwiper] Número de películas (después del posible ajuste):", peliculasData.length);


    function updateSlideContent(slideElement, peliculaData) {
        if (!slideElement || !peliculaData) {
            // console.warn("updateSlideContent: Faltan slideElement o peliculaData.");
            return;
        }
        const imgEl = slideElement.querySelector('.hero-slide-background-image');
        const titleEl = slideElement.querySelector('.hero-slide-title');
        if (imgEl) {
            // Asegurarse de que peliculaData.titulo existe antes de usarlo en el alt
            imgEl.alt = `Imagen de fondo de ${peliculaData && peliculaData.titulo ? peliculaData.titulo : 'Película'}`;
        }
        if (titleEl && peliculaData && peliculaData.titulo) {
            titleEl.textContent = peliculaData.titulo;
        } else if (titleEl) {
            titleEl.textContent = 'Sin Título';
        }
    }

    if (heroSwiperElement && peliculasData && peliculasData.length > 0) { // Verificamos peliculasData aquí también
        console.log("[HeroSwiper] Elemento y datos (array) existen. Procediendo a inicializar slides.");

        const swiperSlides = Array.from(heroSwiperElement.querySelectorAll('.swiper-slide'));
        console.log("[HeroSwiper] swiperSlides encontrados:", swiperSlides.length);

        swiperSlides.forEach((slide, domIndex) => { // Añadí domIndex para depurar
            const slideIndexAttr = slide.dataset.slideIndex;
            // console.log(`[HeroSwiper] Procesando slide DOM ${domIndex}, data-slide-index: ${slideIndexAttr}`);
            if (slideIndexAttr !== undefined) {
                const slideIndex = parseInt(slideIndexAttr, 10);
                if (!isNaN(slideIndex) && peliculasData[slideIndex]) {
                    updateSlideContent(slide, peliculasData[slideIndex]);
                } else {
                    // console.warn(`[HeroSwiper] No se encontraron datos para el slide con data-slide-index: ${slideIndexAttr}`);
                }
            } else {
                console.warn(`[HeroSwiper] Slide DOM ${domIndex} NO tiene 'data-slide-index'. Esto es crucial.`);
            }
        });

        if (swiperSlides.length > 0) {
            console.log("[HeroSwiper] Intentando inicializar Swiper...");
            try {
                const heroSwiper = new Swiper(heroSwiperElement, {
                    modules: [Navigation, Pagination], // Empecemos solo con estos
                    slidesPerView: 1,
                    spaceBetween: 0,
                    loop: peliculasData.length > 1,
                    grabCursor: peliculasData.length > 1,
                    pagination: {
                        el: '.hero-swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.hero-swiper-button-next',
                        prevEl: '.hero-swiper-button-prev',
                    },
                    watchOverflow: true,
                    observer: true,
                    observeParents: true,
                    on: {
                        init: function (swiper) {
                            console.log('[HeroSwiper] Evento: init disparado. Swiper instance:', swiper);
                            console.log('[HeroSwiper] init - realIndex:', swiper.realIndex, 'activeIndex:', swiper.activeIndex);
                            if (swiper.navigation) swiper.navigation.update();
                            if (swiper.pagination) swiper.pagination.update();

                            const activeRealIndex = swiper.realIndex;
                            const activeSlideElement = swiper.slides[swiper.activeIndex];
                            if (activeSlideElement && peliculasData[activeRealIndex]) {
                                updateSlideContent(activeSlideElement, peliculasData[activeRealIndex]);
                            }
                        },
                        slideChange: function (swiper) {
                            console.log('[HeroSwiper] Evento: slideChange disparado. Nuevo realIndex:', swiper.realIndex);
                        },
                        slideChangeTransitionEnd: function (swiper) {
                            if (swiper.pagination) {
                                swiper.pagination.render();
                                swiper.pagination.update();
                            }
                            if (swiper.navigation) swiper.navigation.update();

                            const activeRealIndex = swiper.realIndex;
                            const activeSlideElement = swiper.slides[swiper.activeIndex];
                            if (activeSlideElement && peliculasData[activeRealIndex]) {
                                updateSlideContent(activeSlideElement, peliculasData[activeRealIndex]);
                            }
                        },
                        resize: function (swiper) {
                             swiper.slides.forEach((slide) => {
                                const slideIndexAttr = slide.dataset.slideIndex;
                                if (slideIndexAttr !== undefined) {
                                    const slideIndex = parseInt(slideIndexAttr, 10);
                                    if (!isNaN(slideIndex) && peliculasData[slideIndex]) {
                                        updateSlideContent(slide, peliculasData[slideIndex]);
                                    }
                                }
                            });
                            if (swiper.navigation) swiper.navigation.update();
                            if (swiper.pagination) swiper.pagination.update();
                        },
                        navigationPrev: function() {
                            console.log('[HeroSwiper] Click en botón Prev detectado por Swiper');
                        },
                        navigationNext: function() {
                            console.log('[HeroSwiper] Click en botón Next detectado por Swiper');
                        },
                        touchStart: function(swiper, event) {
                            console.log('[HeroSwiper] Evento: touchStart (inicio de drag)', event);
                        },
                        sliderMove: function(swiper, event) {
                            console.log('[HeroSwiper] Evento: sliderMove (arrastrando)', event);
                        }
                    }
                });
                console.log("[HeroSwiper] Swiper inicializado exitosamente. Instancia:", heroSwiper);

            } catch (e) {
                console.error("[HeroSwiper] ERROR AL INICIALIZAR SWIPER:", e);
                console.error(e.stack);
            }
        } else {
            console.warn("[HeroSwiper] No se encontraron elementos .swiper-slide para inicializar.");
        }
    } else {
        if (!heroSwiperElement) {
            console.warn("[HeroSwiper] Elemento .hero-movie-swiper no encontrado en el DOM.");
        }
        // Se verifica si peliculasData existe y si su longitud es 0
        if (!peliculasData || peliculasData.length === 0) {
            console.warn("[HeroSwiper] No hay datos de películas (peliculasData está vacío o no es un array con elementos).");
        }
        console.warn("[HeroSwiper] Swiper no se inicializará.");
    }
});