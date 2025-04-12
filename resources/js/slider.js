document.addEventListener("DOMContentLoaded", (event) => {
    gsap.registerPlugin(Draggable);

    function initSlider() {
        const wrapper = document.querySelector('[data-slider="list"]');
        console.log(wrapper);
        const slides = gsap.utils.toArray('[data-slider="slide"]');

        const nextButton = document.querySelector('[data-slider="button-next"]');
        const prevButton = document.querySelector('[data-slider="button-prev"]');
        const totalElement = document.querySelector('[data-slide-count="total"]');
        const stepElement = document.querySelector('[data-slide-count="step"]');
        const stepsParent = stepElement.parentElement;

        let activeElement;
        const totalSlides = slides.length;

        // Actualizar el texto de total de slides, agregar 0 si es menor que 10
        totalElement.textContent = totalSlides < 10 ? `0${totalSlides}` : totalSlides;

        // Crear los elementos de los pasos dinámicamente
        stepsParent.innerHTML = ""; // Limpiar cualquier paso existente
        slides.forEach((_, index) => {
            const stepClone = stepElement.cloneNode(true); // Clonar el paso único
            stepClone.textContent = index + 1 < 10 ? `0${index + 1}` : index + 1;
            stepsParent.appendChild(stepClone); // Añadir al contenedor principal
        });

        // Pasos generados dinámicamente
        const allSteps = stepsParent.querySelectorAll('[data-slide-count="step"]');

        const loop = horizontalLoop(slides, {
            paused: true,
            draggable: true,
            center: false,
            onChange: (element, index) => {
                // Añadimos la clase activa al siguiente elemento
                activeElement && activeElement.classList.remove("active");
                const nextSibling = element.nextElementSibling || slides[0];
                nextSibling.classList.add("active");
                activeElement = nextSibling;

                // Mover el número al lugar correcto
                gsap.to(allSteps, {
                    y: `${-100 * index}%`,
                    ease: "power3",
                    duration: 0.45,
                });
            },
        });

        // Similar a lo anterior, restamos 1 al índice clicado porque nuestro diseño está desplazado
        slides.forEach((slide, i) =>
            slide.addEventListener("click", () =>
                loop.toIndex(i - 1, { ease: "power3", duration: 0.725 })
            )
        );

        nextButton.addEventListener("click", () =>
            loop.next({ ease: "power3", duration: 0.725 })
        );
        prevButton.addEventListener("click", () =>
            loop.previous({ ease: "power3", duration: 0.725 })
        );
    }

    function horizontalLoop(items, config) {
        let timeline;
        items = gsap.utils.toArray(items);
        config = config || {};
        gsap.context(() => {
            let onChange = config.onChange,
                lastIndex = 0,
                tl = gsap.timeline({
                    repeat: config.repeat,
                    onUpdate: onChange && function () {
                        let i = tl.closestIndex();
                        if (lastIndex !== i) {
                            lastIndex = i;
                            onChange(items[i], i);
                        }
                    },
                    paused: config.paused,
                    defaults: { ease: "none" },
                    onReverseComplete: () =>
                        tl.totalTime(tl.rawTime() + tl.duration() * 100),
                }),
                length = items.length,
                startX = items[0].offsetLeft,
                times = [],
                widths = [],
                spaceBefore = [],
                xPercents = [],
                curIndex = 0,
                indexIsDirty = false,
                center = config.center,
                pixelsPerSecond = (config.speed || 1) * 100,
                snap = config.snap === false
                    ? (v) => v
                    : gsap.utils.snap(config.snap || 1), // Ajuste de snap para un movimiento más fluido
                timeOffset = 0,
                container =
                    center === true
                        ? items[0].parentNode
                        : gsap.utils.toArray(center)[0] || items[0].parentNode,
                totalWidth,
                getTotalWidth = () =>
                    items[length - 1].offsetLeft +
                    (xPercents[length - 1] / 100) * widths[length - 1] -
                    startX +
                    spaceBefore[0] +
                    items[length - 1].offsetWidth *
                        gsap.getProperty(items[length - 1], "scaleX") +
                    (parseFloat(config.paddingRight) || 0),
                populateWidths = () => {
                    let b1 = container.getBoundingClientRect(),
                        b2;
                    items.forEach((el, i) => {
                        widths[i] = parseFloat(gsap.getProperty(el, "width", "px"));
                        xPercents[i] = snap(
                            (parseFloat(gsap.getProperty(el, "x", "px")) / widths[i]) *
                            100 + gsap.getProperty(el, "xPercent")
                        );
                        b2 = el.getBoundingClientRect();
                        spaceBefore[i] = b2.left - (i ? b1.right : b1.left);
                        b1 = b2;
                    });
                    gsap.set(items, {
                        // Convertir "x" en "xPercent" para hacer todo más responsivo
                        xPercent: (i) => xPercents[i],
                    });
                    totalWidth = getTotalWidth();
                },
                timeWrap,
                populateOffsets = () => {
                    timeOffset = center
                        ? (tl.duration() * (container.offsetWidth / 2)) / totalWidth
                        : 0;
                    center &&
                        times.forEach((t, i) => {
                            times[i] = timeWrap(
                                tl.labels["label" + i] +
                                (tl.duration() * widths[i]) / 2 / totalWidth -
                                timeOffset
                            );
                        });
                },
                getClosest = (values, value, wrap) => {
                    let i = values.length,
                        closest = 1e10,
                        index = 0,
                        d;
                    while (i--) {
                        d = Math.abs(values[i] - value);
                        if (d > wrap / 2) {
                            d = wrap - d;
                        }
                        if (d < closest) {
                            closest = d;
                            index = i;
                        }
                    }
                    return index;
                },
                populateTimeline = () => {
                    let i, item, curX, distanceToStart, distanceToLoop;
                    tl.clear();
                    for (i = 0; i < length; i++) {
                        item = items[i];
                        curX = (xPercents[i] / 100) * widths[i];
                        distanceToStart =
                            item.offsetLeft + curX - startX + spaceBefore[0];
                        distanceToLoop =
                            distanceToStart +
                            widths[i] * gsap.getProperty(item, "scaleX");
                        tl.to(
                            item,
                            {
                                xPercent: snap(
                                    ((curX - distanceToLoop) / widths[i]) * 100
                                ),
                                duration: distanceToLoop / pixelsPerSecond,
                            },
                            0
                        )
                            .fromTo(
                                item,
                                {
                                    xPercent: snap(
                                        ((curX - distanceToLoop + totalWidth) / widths[i]) *
                                        100
                                    ),
                                },
                                {
                                    xPercent: xPercents[i],
                                    duration:
                                        (curX -
                                            distanceToLoop +
                                            totalWidth -
                                            curX) / pixelsPerSecond,
                                    immediateRender: false,
                                },
                                distanceToLoop / pixelsPerSecond
                            )
                            .add("label" + i, distanceToStart / pixelsPerSecond);
                        times[i] = distanceToStart / pixelsPerSecond;
                    }
                    timeWrap = gsap.utils.wrap(0, tl.duration());
                },
                refresh = (deep) => {
                    let progress = tl.progress();
                    tl.progress(0, true);
                    populateWidths();
                    deep && populateTimeline();
                    populateOffsets();
                    deep && tl.draggable
                        ? tl.time(times[curIndex], true)
                        : tl.progress(progress, true);
                },
                onResize = () => refresh(true),
                proxy;
            gsap.set(items, { x: 0 });
            populateWidths();
            populateTimeline();
            populateOffsets();
            window.addEventListener("resize", onResize);
            
            // Habilitar Draggable
            Draggable.create(wrapper, {
                type: "x",
                bounds: container,
                edgeResistance: 0.65,
                onDrag: function () {
                    let dragPos = this.x / totalWidth;
                    tl.progress(dragPos);
                },
                onThrowUpdate: function () {
                    let dragPos = this.x / totalWidth;
                    tl.progress(dragPos);
                },
                onRelease: function () {
                    tl.play();
                }
            });

            function toIndex(index, vars) {
                vars = vars || {};
                Math.abs(index - curIndex) > length / 2
                    ? (curIndex = getClosest(times, index, totalWidth))
                    : (curIndex = index);
                tl.seek(times[curIndex], true);
                tl.play();
            }
            
            return { toIndex };
        });
    }

    initSlider();
});
