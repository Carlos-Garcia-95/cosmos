// Función para mostrar el modal de detalle
window.mostrar_detalle = function(peliculaId) {

    const modal_detalle = document.getElementById('modal_detalle');

    const pelicula = peliculas[peliculaId];
    const imagen_box = document.getElementById('detalle_imagen_box');
    const titulo_element = document.getElementById('detalle_titulo');
    const estreno_element = document.getElementById('detalle_estreno');
    const duracion_element = document.getElementById('detalle_duracion');
    const sinopsis_element = document.getElementById('sinopsis');
    const comprar_element = document.getElementById('detalle_comprar');


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
};

// Para que la ventana se cierre al presionar Escape
const modal_detalle = document.getElementById('modal_detalle');

document.addEventListener('keydown', function(event) {
    if ((event.key === 'Escape' || event.keyCode === 27) && modal_detalle.classList.contains('visible')) {
        
        event.preventDefault();

        // Hide the modal by reversing the class logic from mostrar_detalle
        modal_detalle.classList.remove('visible');
        modal_detalle.classList.add('hidden');

    }
});

// Para que la ventana se cierre al clicar fuera del modal
modal_detalle.addEventListener('click', function(event) {
    if (event.target === modal_detalle && modal_detalle.classList.contains('visible')) {
        modal_detalle.classList.remove('visible');
        modal_detalle.classList.add('hidden');

    }
});
