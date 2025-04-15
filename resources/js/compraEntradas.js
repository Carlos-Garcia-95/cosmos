const container = document.querySelector('.container');
const seats = document.querySelectorAll('.rowr .seat:not(.occupied)'); // Selector ajustado para tu estructura
const count = document.getElementById('count');
const total = document.getElementById('total');
const movieSelect = document.getElementById('movie');
const seccionCompra = document.getElementById('seccionCompra');
const cerrarCompraBtn = document.getElementById('cerrarCompra'); // Asegúrate de que este ID exista en tu HTML

// Inicializa la película seleccionada y los asientos almacenados
populateUI();

let ticketPrice = +movieSelect.value;

// Guarda los datos de la película seleccionada
function setMovieData(movieIndex, moviePrice) {
    localStorage.setItem('selectedMovieIndex', movieIndex);
    localStorage.setItem('selectedMoviePrice', moviePrice);
}

// Actualiza el total y la cantidad de asientos seleccionados
export function updateSelectedCount() {
    const selectedSeats = document.querySelectorAll('.rowr .seat.selected'); // Selector ajustado
    const seatsIndex = [...selectedSeats].map(seat => [...seats].indexOf(seat));
    localStorage.setItem('selectedSeats', JSON.stringify(seatsIndex));

    const selectedSeatsCount = selectedSeats.length;
    count.innerText = selectedSeatsCount;
    total.innerText = selectedSeatsCount * ticketPrice;
}

// Rellena la interfaz con los datos guardados en el localStorage
function populateUI() {
    const selectedSeats = JSON.parse(localStorage.getItem('selectedSeats'));

    if (selectedSeats !== null && selectedSeats.length > 0) {
        seats.forEach((seat, index) => {
            if (selectedSeats.indexOf(index) > -1) {
                seat.classList.add('selected');
            }
        });
    }

    const selectedMovieIndex = localStorage.getItem('selectedMovieIndex');
    if (selectedMovieIndex !== null) {
        movieSelect.selectedIndex = selectedMovieIndex;
    }
}

// Cuando cambia la película seleccionada
movieSelect.addEventListener('change', e => {
    ticketPrice = +e.target.value;
    setMovieData(e.target.selectedIndex, e.target.value);
    updateSelectedCount();
});

// Cuando se hace clic en un asiento
container.addEventListener('click', e => {
    if (e.target.classList.contains('seat') && !e.target.classList.contains('occupied')) {
        e.target.classList.toggle('selected');
        updateSelectedCount();
    }
});

// Event listener para el botón "Comprar Entradas" (mostrar/ocultar el div)
// La lógica ahora está directamente dentro del primer event listener DOMContentLoaded

// Event listener para el botón de cerrar (si existe)
if (cerrarCompraBtn && seccionCompra) {
    cerrarCompraBtn.addEventListener('click', () => {
        seccionCompra.classList.add('hidden');
        seccionCompra.classList.remove('visible');
    });
}

// Para cerrar el div haciendo clic fuera de él (si quieres este comportamiento)
window.addEventListener('click', (event) => {
    if (event.target === seccionCompra) {
        seccionCompra.classList.add('hidden');
        seccionCompra.classList.remove('visible');
    }
});

// Actualiza el total y la cantidad de asientos seleccionados al cargar la página
updateSelectedCount();