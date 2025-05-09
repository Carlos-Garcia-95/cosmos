document.addEventListener('DOMContentLoaded', () => {
    const seccionCompra = document.getElementById('seccionCompra');
<<<<<<< HEAD
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
            
=======
    const cerrarCompraBtn = document.getElementById('cerrarCompra'); 

    // Función para mostrar el modal de compra
    window.mostrarCompra = function(peliculaId) { // Convertida en función global
        console.log("Película ক্লিক করা হয়েছে:", peliculaId);
        // Aquí deberías obtener los detalles de la película usando el ID (peliculaId)
        // y actualizar el contenido de seccionCompra con esos detalles.

        // Por ejemplo, podrías hacer algo como esto:
        const pelicula = peliculas[peliculaId]; // Acceder a los datos de la película
        console.log(peliculas);

        //Seccion de compra es un div, no olvides agregar el contenido de la pelicula
        seccionCompra.innerHTML = `
            <h2>Comprar Entrada para: ${pelicula.titulo}</h2>
            <p>ID de la película: ${peliculaId}</p>
            <p>Aquí puedes agregar más detalles como la sinopsis, horario, etc.</p>
            <button id="cerrarCompra">Cerrar</button>
        `;

        if (seccionCompra.classList.contains('hidden')) {
            seccionCompra.classList.remove('hidden');
            seccionCompra.classList.add('visible');
        }


        // Event listener para el botón de cerrar.  Se define aquí para asegurar que el elemento cerrarCompraBtn existe.
        const cerrarCompraBtn = document.getElementById('cerrarCompra');
        if (cerrarCompraBtn && seccionCompra) {
            cerrarCompraBtn.addEventListener('click', (event) => { // Añadido el parámetro event
                event.preventDefault();
>>>>>>> 0d7e958abb4f86ae38cadfcfdb18d434dfe0911a
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                setTimeout(() => {
                    seccionCompra.classList.add('hidden');
                }, 500);
            });
        }
    };
});