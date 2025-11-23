import {showLoading, notyf} from './notify/Config.js';
import {ModalManager} from './utils/ModalManager.js';

const modalManager = new ModalManager();
// Gestión de Carreras
document.addEventListener('DOMContentLoaded', function () {
    loadCarreras();

    // Formulario de carrera
    const formCarrera = document.getElementById('formCarrera');
    if (formCarrera) {
        formCarrera.addEventListener('submit', handleSubmitCarrera);
    }
});

// Cargar carreras
async function loadCarreras() {
    const tbody = document.getElementById('carrerasTableBody');

    try {
        showLoading(true);
        const response = await fetch('../php/carreras_api.php?action=list');
        const data = await response.json();

        showLoading(false);
        if (data.success) {
            renderCarreras(data.data);
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">Error al cargar carreras</td></tr>';
        }
    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Error al cargar carreras</td></tr>';
    } finally {
        showLoading(false);
    }
}

// Renderizar carreras en tabla
function renderCarreras(carreras) {
    const tbody = document.getElementById('carrerasTableBody');

    if (carreras.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No se encontraron carreras</td></tr>';
        return;
    }

    tbody.innerHTML = carreras.map(carrera => `
        <tr>
            <td><span class="badge badge-primary">${carrera.codigo}</span></td>
            <td><strong>${carrera.nombre}</strong></td>
            <td>${carrera.duracion_semestres} semestres</td>
            <td>${carrera.descripcion || '-'}</td>
            <td>
                <div class="flex gap-1">
                    <button class="btn btn-sm btn-secondary" onclick="editCarrera(${carrera.id})" title="Editar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteCarrera(${carrera.id})" title="Eliminar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}


// Manejar envío de formulario
async function handleSubmitCarrera(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const carrera_id = formData.get('carrera_id');
    const action = carrera_id ? 'update' : 'create';

    // Validaciones
    const codigo = formData.get('codigo');
    const nombre = formData.get('nombre');
    const duracion = formData.get('duracion_semestres');

    if (!codigo || !nombre || !duracion) {
        notyf.error('Todos los campos obligatorios deben ser completados');
        return;
    }

    if (duracion < 1) {
        notyf.error('La duración debe ser al menos 1 semestre');
        return;
    }

    formData.set('action', action);

    modalManager.openInfo(
        'Confirmar Acción',
        `¿Estás seguro de que deseas ${action === 'create' ? 'crear' : 'actualizar'} esta carrera?`,
        async () => {
            await submitCarreraForm(formData, form);
            modalManager.closeModal(ModalManager.ModalType.INFO);
        }
    )
}

// Enviar formulario de carrera
async function submitCarreraForm(formData, form) {
    try {
        showLoading(true);
        const response = await fetch('../php/carreras_api.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        showLoading(false);
        if (data.success) {
            notyf.success(data.message);
            closeModal('modalCarrera');
            await loadCarreras();
            form.reset();

            modalManager.openSuccess(
                'Operación Exitosa',
                `La carrera ha sido ${formData.get('action') === 'create' ? 'creada' : 'actualizada'} exitosamente.`,
                () => {
                    loadCarreras();
                    modalManager.closeModalPop();
                });

        } else {
            notyf.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        notyf.error('Error al guardar la carrera');
    } finally {
        showLoading(false);
    }
}

// Confirmar eliminación de carrera
async function confirmDeleteCarrera(id) {
    try {
        showLoading(true);
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        const response = await fetch('../php/carreras_api.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        showLoading(false);
        if (data.success) {
            notyf.success(data.message);
            await loadCarreras();
        } else {
            notyf.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        notyf.error('Error al eliminar la carrera');
    } finally {
        showLoading(false);
    }
}


// Editar carrera
window.editCarrera = async (id) => {
    try {
        showLoading(true);
        const response = await fetch(`../php/carreras_api.php?action=get&id=${id}`);
        const data = await response.json();
        showLoading(false);

        if (data.success) {
            const carrera = data.data;

            document.getElementById('carrera_id').value = carrera.id;
            document.getElementById('codigo').value = carrera.codigo;
            document.getElementById('nombre').value = carrera.nombre;
            document.getElementById('duracion_semestres').value = carrera.duracion_semestres;
            document.getElementById('descripcion').value = carrera.descripcion || '';

            document.getElementById('modalCarreraTitle').textContent = 'Editar Carrera';
            openModal('modalCarrera');
        } else {
            notyf.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al cargar la carrera', 'error');
    } finally {
        showLoading(false);
    }
}

// Eliminar carrera
window.deleteCarrera = async (id) => {
    modalManager.openWarning(
        'Eliminar Carrera',
        '¿Estás seguro de que deseas eliminar esta carrera? Esta acción no se puede deshacer.',
        async () => {
            await confirmDeleteCarrera(id);
            modalManager.closeModalPop();
        });
};

