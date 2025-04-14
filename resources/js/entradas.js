document.addEventListener('DOMContentLoaded', () => {
    const botonCompra = document.getElementById('mostrarCompra');
    const seccionCompra = document.getElementById('seccionCompra');
    const cerrarCompraBtn = document.getElementById('cerrarCompra'); // Ahora este botón existe

    // Mostrar el modal al hacer clic en "COMPRAR ENTRADAS"
    if (botonCompra && seccionCompra) {
        botonCompra.addEventListener('click', (event) => {
            event.preventDefault(); // Evitar el comportamiento predeterminado del enlace
            seccionCompra.classList.toggle('hidden');
            seccionCompra.classList.toggle('visible');
            
            // Si el modal se muestra, actualizamos el conteo de asientos seleccionados
            if (seccionCompra.classList.contains('visible')) {
                updateSelectedCount();
            }
        });
    }

    // Event listener para el botón de cerrar
    if (cerrarCompraBtn && seccionCompra) {
        cerrarCompraBtn.addEventListener('click', () => {
            seccionCompra.classList.add('hidden');
            seccionCompra.classList.remove('visible');
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
