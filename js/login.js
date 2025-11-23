import {login} from './auth/Auth.js';
import {notyf} from './notify/Config.js';

const showLoading = (isVisible) => {
    if (isVisible) {
        document.getElementById("loading").style.display = "block";
    } else {
        document.getElementById("loading").style.display = "none";
    }
};

document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');

    loginForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            notyf.open({type: 'warning', message: 'Por favor, complete todos los campos'});
            return;
        }

        try {
            showLoading(true);
            const response = await login(email, password);

            if (response.success) {
                // Enviar POST con FormData para que auth.php reciba action y email correctamente
                const form = new FormData();
                form.append('action', 'login');
                form.append('email', email);

                const responseApi = await fetch('../php/auth.php', {
                    method: 'POST',
                    body: form
                });
                const dataApi = await responseApi.json();

                if (dataApi.success) {
                    notyf.success("¡Inicio de sesión exitoso!");
                    window.location.href = dataApi.data.redirect; // <- Redirigir según la respuesta del servidor
                } else {
                    notyf.error(dataApi.message || 'Error en autenticación local');
                }
            } else {
                notyf.error(response.message || 'Credenciales inválidas');
            }
        } catch (error) {
            console.error(error);
            notyf.error("Error de red o del servidor");
        } finally {
            showLoading(false);
        }
    });
});
