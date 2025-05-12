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

            let primera = true;
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

                    // Los asientos de la primera hora se mostrarán por defecto
                    if (primera) {
                        generar_asientos(sesion_hora.id);
                        primera = false;
                    }
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

    const sesion_asientos = `/recuperar_asientos/id_sesion=${id_sesion}`; // Example using peliculaId in the path
    const seccion_asientos = document.getElementById('seccion_asientos');
    const superior_izquierda = document.getElementById('superior_izquierda');

    // Mensaje de carga (por si tarda en cargar la petición)
    superior_izquierda.innerHTML = "<p>Cargando asientos...</p>";

    fetch(sesion_asientos)
        .then(response => {
            // Comprobar si la respuesta fue exitosa
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response.json();
        })
        .then(datos_sesion => {

            // Div container
            let container;
            if (container = document.getElementById('container')) {
                container.innerHTML = "";
            } else {
                container = document.createElement('div');
                container.classList.add('container');
                container.id = 'container';
            }
            

            superior_izquierda.innerHTML = "";

            // Generar los datos de la sesión seleccionada
            generar_datos_sesion(datos_sesion);

            // Generar géneros
            generar_generos_sesion(datos_sesion);

            // Generar leyenda de asientos
            generar_leyenda();

            seccion_asientos.appendChild(container);

            const asientos = datos_sesion.asientos;

            // Comprobar que hemos recuperado correctamente los asientos
            if (!asientos || asientos.length === 0) {
                console.warn("No hay datos de asientos para mostrar.");
                container.innerHTML = '<p>No hay asientos disponibles para esta sesión.</p>';
                return;
            }

            const asientosMap = new Map();
            let numFilas = 0;
            let numColumnas = 0;

            // Crear mapa de asientos
            asientos.forEach(asiento => {
                // Ignorar asientos con coordenadas inválidas
                if (asiento.fila > 0 && asiento.columna > 0) {
                    // Crear una clave única para cada asiento basada en su fila y columna
                    const key = `${asiento.fila}-${asiento.columna}`;
                    asientosMap.set(key, asiento);
        
                    // Actualizar las dimensiones máximas encontradas
                    if (asiento.fila > numFilas) numFilas = asiento.fila;
                    if (asiento.columna > numColumnas) numColumnas = asiento.columna;
                }
            });

            container.innerHTML = '';
            const screen = document.createElement('div');
            screen.classList.add('screen');
            container.appendChild(screen);

            const fragment = document.createDocumentFragment();

            // Generar filas y columnas, dejando espacios vacíos en los asientos que no tengan esos valores
            // Filas
            for (let i = 1; i <= numFilas; i++) {
                const filaDiv = document.createElement('div');
                filaDiv.classList.add('row');
        
                // Columnas
                for (let j = 1; j <= numColumnas; j++) {
                    const key = `${i}-${j}`;
                    const asientoData = asientosMap.get(key); // Búsqueda O(1) en el Map
        
                    const elementoCelda = document.createElement('div');
        
                    // Generar asiento
                    if (asientoData) {
                        elementoCelda.classList.add('seat');
                        // Guardar información útil en atributos data-*
                        elementoCelda.id = asientoData.id_asiento;
                        elementoCelda.fila = i;
                        elementoCelda.columna = j;
                        elementoCelda.estado = asientoData.estado;
        
                        // Aplicar clase según el estado del asiento
                        switch (asientoData.estado) {
                            // Disponible
                            case 1:
                                elementoCelda.classList.add('available');
                                break;
                            // Ocupado
                            case 2:
                                elementoCelda.classList.add('occupied');
                                break;
                            // Desconocido
                            default:
                                break;
                        }
        
                    } else {
                        // Es un hueco / pasillo
                        elementoCelda.classList.add('gap');
                    }
        
                    // Añadir la celda a la fila
                    filaDiv.appendChild(elementoCelda);
                }
                // Añadir la fila al fragmento
                fragment.appendChild(filaDiv);
            }

            // Añadir el fragmento al container
            container.appendChild(fragment);
        })
        .catch(error => {
            // Detectar errores
            console.error('Error al obtener los asientos de la sesión', error);

            if (seccion_asientos) {
                seccion_asientos.innerHTML = `<p>Error al cargar los asientos. Por favor, inténtelo de nuevo</p>`;
            }
        });
}



// Generar leyenda de los asientos
function generar_leyenda() {
    const inferior = document.getElementById('inferior');
    inferior.innerHTML = "";
    const leyenda = document.createElement('ul');
    leyenda.classList.add('showcase');

    let elemento1_li = document.createElement('li');
    let elemento1_div = document.createElement('div');
    elemento1_div.classList.add('seat');
    let elemento1_small = document.createElement('small');
    elemento1_small.innerHTML = "Disponible";
    elemento1_li.appendChild(elemento1_div);
    elemento1_li.appendChild(elemento1_small);

    let elemento2_li = document.createElement('li');
    let elemento2_div = document.createElement('div');
    elemento2_div.classList.add('seat');
    elemento2_div.classList.add('selected');
    let elemento2_small = document.createElement('small');
    elemento2_small.innerHTML = "Seleccionado";
    elemento2_li.appendChild(elemento2_div);
    elemento2_li.appendChild(elemento2_small);

    let elemento3_li = document.createElement('li');
    let elemento3_div = document.createElement('div');
    elemento3_div.classList.add('seat');
    elemento3_div.classList.add('occupied');
    let elemento3_small = document.createElement('small');
    elemento3_small.innerHTML = "Ocupado";
    elemento3_li.appendChild(elemento3_div);
    elemento3_li.appendChild(elemento3_small);

    leyenda.appendChild(elemento1_li);
    leyenda.appendChild(elemento2_li);
    leyenda.appendChild(elemento3_li);

    inferior.appendChild(leyenda);
}



// Generar los géneros de la película seleccionada
function generar_generos_sesion(datos_sesion) {
    const columna_derecha = document.getElementById('columna_derecha');
    columna_derecha.innerHTML = "";

    const generos = datos_sesion.pelicula.generos;

    generos.forEach(genero => {
        const genero_div = document.createElement('div');
        genero_div.classList.add('header_genero');
        genero_div.innerHTML = genero.genero;
        columna_derecha.appendChild(genero_div);
    });
}




// Generar los datos de la sesión seleccionada
function generar_datos_sesion(datos_sesion) {
    const superior_izquierda = document.getElementById('superior_izquierda');
    superior_izquierda.innerHTML = "";
    const inferior_izquierda = document.getElementById('inferior_izquierda');
    inferior_izquierda.innerHTML = "";
    const container = document.createElement('div');
    container.classList.add('movie-container');

    const fecha_div = document.createElement('div');
    fecha_div.classList.add('header_div');
    const fecha = document.createElement('div');
    fecha.classList.add('fecha_asientos');
    fecha.innerHTML = "<span class='header_titulo'>Fecha: </span> " + datos_sesion.fecha.fecha;
    fecha_div.appendChild(fecha);

    const hora_div = document.createElement('div');
    hora_div.classList.add('header_div');
    const hora = document.createElement('div');
    hora.classList.add('hora_asientos');
    hora.innerHTML = "<span class='header_titulo'>Hora: </span> " + datos_sesion.hora.hora;
    hora_div.appendChild(hora);

    const pelicula = document.createElement('div');
    pelicula.classList.add('pelicula_asientos');
    pelicula.innerHTML = datos_sesion.pelicula.titulo;

    const duracion_div = document.createElement('div');
    duracion_div.classList.add('header_div');
    const duracion = document.createElement('div');
    duracion.classList.add('duracion_asientos');
    duracion.innerHTML = "<span class='header_titulo'>Duración: </span> " + datos_sesion.pelicula.duracion + " min";
    duracion_div.appendChild(duracion);

    const sala_div = document.createElement('div');
    sala_div.classList.add('header_div');
    const sala = document.createElement('div');
    sala.classList.add('sala_asientos');
    sala.innerHTML = "<span class='header_titulo'>Sala: </span> " + datos_sesion.sesion.id_sala;
    sala_div.appendChild(sala);

    superior_izquierda.appendChild(pelicula);
    inferior_izquierda.appendChild(fecha_div);
    inferior_izquierda.appendChild(hora_div);
    inferior_izquierda.appendChild(sala_div);
    inferior_izquierda.appendChild(duracion_div);
    
}