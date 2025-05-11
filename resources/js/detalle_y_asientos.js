// Función para mostrar el modal de detalle
window.mostrar_detalle = function (peliculaId) {

    const modal_detalle = document.getElementById('modal_detalle');

    const pelicula = peliculas[peliculaId];
    const imagen_box = document.getElementById('detalle_imagen_box');
    const titulo_element = document.getElementById('detalle_titulo');
    const estreno_element = document.getElementById('detalle_estreno');
    const duracion_element = document.getElementById('detalle_duracion');
    const sinopsis_element = document.getElementById('sinopsis');
    const comprar_btn = document.getElementById('detalle_comprar_btn');


    imagen_box.innerHTML = `
        ${pelicula.poster_url ? `<img class='detalle_imagen' src="${pelicula.backdrop_url}" loading="lazy" alt="${pelicula.titulo}">` : `<p>Póster no disponible</p>`}
    `;

    titulo_element.textContent = pelicula.titulo;
    estreno_element.textContent = `Fecha de Estreno: ${pelicula.fecha_estreno}`;
    duracion_element.textContent = `Duración: ${pelicula.duracion}`;
    sinopsis_element.textContent = pelicula.sinopsis;


    if (modal_detalle.classList.contains('hidden')) {
        modal_detalle.classList.remove('hidden');
        modal_detalle.classList.add('visible');
    }

    // Funcionalidad de botón COMPRAR ENTRADA
    if (comprar_btn && seccionCompra) {
        comprar_btn.addEventListener('click', function () {
            // Oculta el Modal
            modal_detalle.classList.remove('visible');
            modal_detalle.classList.add('hidden');

            // Hace visible el div de elegir sesión
            if (seccionCompra.classList.contains('hidden')) {
                seccionCompra.classList.remove('hidden');
                seccionCompra.classList.add('visible');
            }

            // Lleva la vista al nuevo div creado
            seccionCompra.scrollIntoView({
                behavior: 'smooth'
            });

            // Se llama a la función que genera dinámicamente la sección de selección de asientos
            mostrar_seleccion_asientos(peliculaId);
        });
    }

};


// Para que la ventana se cierre al presionar Escape
if (modal_detalle) {
    document.addEventListener('keydown', function (event) {
        if ((event.key === 'Escape' || event.keyCode === 27) && modal_detalle.classList.contains('visible')) {

            event.preventDefault();

            // Hide the modal by reversing the class logic from mostrar_detalle
            modal_detalle.classList.remove('visible');
            modal_detalle.classList.add('hidden');

        }
    });
}


// Para que la ventana se cierre al clicar fuera del modal
if (modal_detalle) {
    modal_detalle.addEventListener('click', function (event) {
        if (event.target === modal_detalle && modal_detalle.classList.contains('visible')) {
            modal_detalle.classList.remove('visible');
            modal_detalle.classList.add('hidden');

        }
    });
}


// Función para mostrar la sección Compra (solo se activará al presionar COMPRAR ENTRADA)
// Realiza una petición AJAX al controlador X para recuperar las sesiones de la película seleccionada

function mostrar_seleccion_asientos(peliculaId) {
    const seccionCompra = document.getElementById('seccionCompra');
    const seccion_sesiones = seccionCompra ? seccionCompra.querySelector('.seccion_sesiones') : null;

    // Comprobar que el div existe
    if (!seccion_sesiones) {
        console.error("Div '.seccion_sesiones' no encontrado dentro de '#seccionCompra'.");
        return;
    }

    // Mensaje de carga (por si tarda en cargar la petición)
    seccion_sesiones.innerHTML = "<p>Cargando sesiones...</p>";

    // Ruta de controlador
    const sesiones_url = `/recuperar_sesiones/id_pelicula=${peliculaId}`; // Example using peliculaId in the path

    fetch(sesiones_url)
        .then(response => {
            // Comprobar si la respuesta fue exitosa
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(sesiones => {

            // Comprobar si sesiones tiene datos disponibles
            if (!sesiones || sesiones.length === 0) {
                seccion_sesiones.innerHTML = "<p>No hay sesiones disponibles para esta película en los próximos días.</p>";
                return;
            }

            let fila_sesion = '';

            const sesiones_por_dias = {};

            // Ordenar y filtrar las sesiones por días (ahora cada día tendrá varias horas)
            sesiones.forEach(sesion => {
                const fecha = sesion.fecha.fecha;

                if (!sesiones_por_dias[fecha]) {
                    sesiones_por_dias[fecha] = [];
                }

                sesiones_por_dias[fecha].push(sesion);
            });
            
            // Recorrer cada sesión y generar el HTML con los datos recogidos
            Object.entries(sesiones_por_dias).forEach(([dia, sesion]) => {

                const primera_sesion = sesion[0];
                const dia_semana = primera_sesion.dia_semana;
                const dia_calendario = dia;

                // Formatear día de la semana (Domingo -> Dom)
                const dia_semana_format = dia_semana.substring(0, 3); // "Dom"

                // Formatear el día del calendario (YYYY-MM-DD -> DD / MM)
                const dia_array = dia_calendario.split('-');
                const dia_calendario_format = `${dia_array[2]} / ${dia_array[1]}`;
                const dia_div = `
                             <div class="day-info">
                                 <div class="day-label">${dia_semana_format}</div>
                                 <div class="date-label">${dia_calendario_format}</div>
                             </div>
                         `;

                // Generar los botones de las horas
                let horas_botones = "";

                // Se recorren las horas de cada día y se genera un botón por cada hora
                // El id del botón será el id de la sesión (para luegor recuperar los asientos)
                sesion.map(sesion_hora => {
                    const hora = sesion_hora.hora.hora;
                    horas_botones += `
                            <div class="session-time" id="${sesion_hora.id}">
                                ${hora}
                            </div>
                        `;
                });

                // Generar div de horas
                const horas_div = `
                          <div class="times-list">
                              ${horas_botones}
                          </div>
                     `;
 
                // Generar el div de sesiones
                fila_sesion += `
                         <div class="session-row">
                             ${dia_div}
                             ${horas_div}
                         </div>
                     `;
            });


            // Rellenar el div de la página con el div de las sesiones generado
            seccion_sesiones.innerHTML = fila_sesion;

            // Recoger todos los botones y añadirles un EventListener para cuando sean clicados
            const horas_sesiones = seccion_sesiones.querySelectorAll('.session-time');
            horas_sesiones.forEach(hora_sesion => {
                hora_sesion.addEventListener('click', function () {
                    // Cuando se clique en el botón se llamará a la función para generar asientos
                    const id_sesion = this.id;
                    generar_asientos(id_sesion);
                });
            });

        })
        .catch(error => {
            // Detectar errores
            console.error('Error al obtener los datos de las sesiones:', error);

            if (seccion_sesiones) {
                seccion_sesiones.innerHTML = `<p>Error al cargar las sesiones. Por favor, inténtelo de nuevo</p>`;
            }
        });

}



function generar_asientos(id_sesion) {
    console.log(id_sesion);
}