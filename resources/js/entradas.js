//import { updateSelectedCount } from './compraEntradas.js';

document.addEventListener('DOMContentLoaded', () => {
    const botonCompra = document.getElementById('mostrarCompra');
    const seccionCompra = document.getElementById('seccionCompra');
    const cerrarCompraBtn = document.getElementById('cerrarCompra'); // Ahora este botón existe

    // Mostrar el modal al hacer clic en "COMPRAR ENTRADAS"
    if (botonCompra && seccionCompra) {
        botonCompra.addEventListener('click', (event) => {
            //event.preventDefault(); // Evitar el comportamiento predeterminado del enlace
            
            if (seccionCompra.classList.contains('hidden')) {
                seccionCompra.classList.remove('hidden');
                seccionCompra.classList.add('visible');
            }
            
            // Si el modal se muestra, actualizamos el conteo de asientos seleccionados
            // Creo que es innecesario, y rompe la funcionalidad
            /* if (seccionCompra.style.display === 'flex') {
                updateSelectedCount();
            } */
        });
    }

    // Event listener para el botón de cerrar
    // TODO -> Transición más suave al cerrar el div de comprar entradas
    if (cerrarCompraBtn && seccionCompra) {
        
        
        cerrarCompraBtn.addEventListener('click', () => {
            setTimeout(() => {
                seccionCompra.classList.add('hidden');
                seccionCompra.classList.remove('visible');
            }, 500);
        });
    }

    // Para cerrar el modal al hacer clic fuera de él
    window.addEventListener('click', (event) => {
        if (event.target === seccionCompra) {
            seccionCompra.classList.add('hidden');
            seccionCompra.classList.remove('visible');
        }
    });
});
