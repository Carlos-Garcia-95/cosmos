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
        
        });
    }

    // Event listener para el botón de cerrar
    // TODO -> Transición más suave al cerrar el div de comprar entradas
    if (cerrarCompraBtn && seccionCompra) {
        
        
        cerrarCompraBtn.addEventListener('click', () => {
                seccionCompra.classList.add('hidden');
        });
    }

});
