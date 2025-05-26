// Lógica de mostrar el mensaje de resultado

document.addEventListener('DOMContentLoaded', function() {
    const flashMessage = document.getElementById('flash-message');
    if (flashMessage && flashMessage.textContent.trim() !== '') {
        flashMessage.classList.add('show');
        setTimeout(function() {
            flashMessage.classList.remove('show');
            setTimeout(() => {
                flashMessage.textContent = '';
            }, 500);
        }, 3000);
    }
});