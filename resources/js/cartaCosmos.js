// Código JavaScript para elementos interactivos de la página.
// Asegúrate de que este código se carga DESPUÉS de que los elementos HTML existan (ej: al final del body o dentro de DOMContentLoaded).

document.addEventListener('DOMContentLoaded', () => {

        const mostrarMenusBtn = document.getElementById('mostrarMenus');
        const seccionMenus = document.getElementById('seccionMenus');
        const cerrarMenusBtn = document.getElementById('cerrarMenus');
    
    
        if (mostrarMenusBtn && seccionMenus) {
            mostrarMenusBtn.addEventListener('click', () => {
    
                if (seccionMenus.classList.contains('hidden')) {
                    setTimeout(() => {
                        seccionMenus.classList.remove('hidden');
                        seccionMenus.classList.add('visible');
                    }, 50);
                }
            });
        }
    
    
        if (cerrarMenusBtn && seccionMenus) {
            cerrarMenusBtn.addEventListener('click', () => {
                seccionMenus.classList.remove('visible');
                seccionMenus.classList.add('hidden');
            });
        }


    const menuItems = document.querySelectorAll('.menu-item');

    if (menuItems.length > 0) {
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                item.classList.toggle('is-flipped');
            });
        });
    }

});