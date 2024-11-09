document.addEventListener('DOMContentLoaded', function() {
    const dniInput = document.getElementById('dni');
    const typeSelect = document.getElementById('type');
    const emailInput = document.getElementById('email');

    dniInput.addEventListener('blur', function() {
        const dni = dniInput.value;
        if (dni) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `${checkDniUrl}?dni=${encodeURIComponent(dni)}`, true);

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    typeSelect.innerHTML = '';

                    if (response.exists) {
                        const option1 = document.createElement('option');
                        option1.value = 'Revisión';
                        option1.textContent = 'Revisión';
                        typeSelect.appendChild(option1);
                    } else {
                        const option1 = document.createElement('option');
                        option1.value = 'Primera consulta';
                        option1.textContent = 'Primera consulta';
                        typeSelect.appendChild(option1);
                    }
                }
            };

            xhr.send();
        }
    });

    emailInput.addEventListener('blur', function() {
        const email = emailInput.value;
        if (email) {
            const regex = /^\S+@\S+\.\S+$/;
            if (!regex.test(email)) {
                alert('Por favor, introduce una dirección de email válida.');
                emailInput.focus();
            }
        }
    });
});