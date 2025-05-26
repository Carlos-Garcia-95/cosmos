
document.addEventListener('DOMContentLoaded', function () {
    // Menú Hamburguesa - Lógica
    const hamburgerButton = document.getElementById('hamburgerButton');
    const navLinks = document.getElementById('headerNavLinks');

    if (hamburgerButton && navLinks) {
        hamburgerButton.addEventListener('click', function () {
            navLinks.classList.toggle('mobile-menu-active');
            hamburgerButton.classList.toggle('open');
            document.body.classList.toggle('mobile-menu-body-lock');

            // Accesibilidad
            const isExpanded = hamburgerButton.getAttribute('aria-expanded') === 'true' || false;
            hamburgerButton.setAttribute('aria-expanded', !isExpanded);

            // If menu is opened, and flash message is there, ensure it's visible
            if (navLinks.classList.contains('mobile-menu-active')) {
                const flashMessageInMenu = navLinks.querySelector('#flash-message');
                if (flashMessageInMenu && flashMessageInMenu.textContent.trim() !== '') {
                    flashMessageInMenu.classList.add('show'); // Re-trigger show if needed
                }
            }
        });

        // Cerrar menú cuando se clica un link
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function (event) {
                if (navLinks.classList.contains('mobile-menu-active')) {
                    // Check if the link is part of a form submission button
                    let parentButton = event.target.closest('button');
                    if (parentButton && parentButton.type === 'submit') {
                        // For form submissions, the page will navigate/reload,
                        // which will naturally reset the menu.
                        // If form is AJAX, you might need to close manually after success.
                        return;
                    }

                    // For other links (navigation, anchor, JS modals), close the menu.
                    navLinks.classList.remove('mobile-menu-active');
                    hamburgerButton.classList.remove('open');
                    document.body.classList.remove('mobile-menu-body-lock');
                    hamburgerButton.setAttribute('aria-expanded', 'false');
                }
            });
        });
    }

    // Original Flash Message Logic (ensure it's not duplicated if already present)
    // This targets the #flash-message, which is now inside #headerNavLinks
    const flashMessage = document.getElementById('flash-message');
    if (flashMessage && flashMessage.textContent.trim() !== '') {
        // If the menu isn't active on page load but flash is there, it might be hidden by .header-buttons {display:none;}
        // The CSS for .success-message.show should make it visible if its parent is visible.
        // If the flash message is inside the mobile menu, it will show when the menu opens.
        // If it's also meant to be visible on desktop, its current placement is fine.

        // This logic primarily handles its appearance and disappearance animation.
        flashMessage.classList.add('show');
        setTimeout(function () {
            flashMessage.classList.remove('show');
            setTimeout(() => {
                if (flashMessage) {
                    flashMessage.textContent = '';
                }
            }, 500);
        }, 3000);
    }
});