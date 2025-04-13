document.addEventListener("DOMContentLoaded", function () {
    
    const joinBtn = document.getElementById('joinCosmosBtn');
    const formWrapper = document.getElementById('multiStepForm');

    if (joinBtn && formWrapper) {
        joinBtn.addEventListener('click', function () {
            formWrapper.style.display = 'flex';
        });
    }

    // Lógica de pasos
    const nextStep1 = document.getElementById('nextStep1');
    const nextStep2 = document.getElementById('nextStep2');

    if (nextStep1 && nextStep2) {
        nextStep1.addEventListener('click', function () {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
        });

        nextStep2.addEventListener('click', function () {
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step3').style.display = 'block';
        });
    }
});
