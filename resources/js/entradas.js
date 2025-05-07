

document.addEventListener('DOMContentLoaded', () => {
    const botonCompra = document.getElementById('mostrarCompra');
    const seccionCompra = document.getElementById('seccionCompra');
    const cerrarCompraBtn = document.getElementById('cerrarCompra');
    const seccionMenus = document.getElementById('seccionMenus');

    // Mostrar el modal al hacer clic en "COMPRAR ENTRADAS"
    if (botonCompra && seccionCompra && seccionMenus) {
        botonCompra.addEventListener('click', () => {
            
            if (seccionCompra.classList.contains('hidden')) {
                seccionCompra.classList.remove('hidden');
                seccionCompra.classList.add('visible');
            }

            if (seccionMenus.classList.contains('visible')) {
                seccionMenus.classList.remove('visible');
                seccionMenus.classList.add('hidden');
            }
            
        });
    }

    // Event listener para el botón de cerrar
    // TODO -> Transición más suave al cerrar el div de comprar entradas
    if (cerrarCompraBtn && seccionCompra) {
        
        cerrarCompraBtn.addEventListener('click', () => {
            
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                
                setTimeout(() => {
                    seccionCompra.classList.add('hidden');
                }, 500);
        });
    }
});
