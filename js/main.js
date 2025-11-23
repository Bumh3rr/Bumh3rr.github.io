
async function logout() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        try {
            const response = await fetch('../../php/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=logout'
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = data.data.redirect;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cerrar sesión');
        }
    }
}

// Funciones para modales
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.classList.remove('closing');

    // Si ya está activo, no hacer nada
    if (modal.classList.contains('active')) return;

    // Hacer visible inmediatamente para que el navegador pueda calcular estilos iniciales
    modal.style.display = 'flex';

    // Forzar reflow / esperar al siguiente frame antes de agregar la clase active
    requestAnimationFrame(() => {
        // pequeña garantía de repaint
        void modal.offsetWidth;
        modal.classList.add('active');
    });
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Agrega clase closing para animar
    modal.classList.add('closing');

    const content = modal.querySelector('.modal-content');
    if (!content) {
        // Si no hay contenido, limpiar inmediatamente
        modal.classList.remove('active', 'closing');
        modal.style.display = '';
        return;
    }

    const onEnd = (e) => {
        if (e.target !== content) return;
        content.removeEventListener('transitionend', onEnd);
        modal.classList.remove('active');
        modal.classList.remove('closing');
        modal.style.display = '';
    };
    content.addEventListener('transitionend', onEnd);

    // Fallback por si no llega el evento transitionend
    setTimeout(() => {
        if (modal.classList.contains('closing')) {
            content.removeEventListener('transitionend', onEnd);
            modal.classList.remove('active', 'closing');
            modal.style.display = '';
        }
    }, 400);

    // Limpiar formulario si existe
    const form = modal.querySelector('form');
    if (form) {
        form.reset();
        const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
        hiddenInputs.forEach(input => input.value = '');
    }
}


// Cerrar modal al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        const modalId = e.target.id;
        closeModal(modalId);
    }
});

// Función para mostrar alertas
function showAlert(message, type = 'success') {

}

// Función para confirmar eliminación
function confirmDelete(message = '¿Estás seguro de que deseas eliminar este elemento?') {
    return confirm(message);
}

// Función para formatear fecha
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('es-MX', options);
}

// Función para formatear hora
function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    return `${hours}:${minutes}`;
}

// Debounce para búsquedas
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Validar email
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function isNameValid(name) {
    const re = /^[a-zA-ZÀ-ÿ\s]{1,40}$/;
    return re.test(name);
}

// Validar teléfono
function isValidPhone(phone) {
    const re = /^[0-9]{10}$/;
    return re.test(phone.replace(/\s/g, ''));
}

// Loading spinner
function showLoading(element) {
    if (element) {
        element.disabled = true;
        element.dataset.originalText = element.textContent;
        element.textContent = 'Cargando...';
    }
}

function hideLoading(element) {
    if (element && element.dataset.originalText) {
        element.disabled = false;
        element.textContent = element.dataset.originalText;
    }
}

// Manejo de errores de fetch
async function handleFetch(url, options = {}) {
    try {
        const response = await fetch(url, options);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Fetch error:', error);
        showAlert('Error al conectar con el servidor', 'error');
        throw error;
    }
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar alertas al hacer clic
    document.addEventListener('click', function(e) {
        if (e.target.closest('.alert')) {
            e.target.closest('.alert').remove();
        }
    });
});
