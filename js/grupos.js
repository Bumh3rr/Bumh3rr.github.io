import {showLoading, notyf} from './notify/Config.js';
import {ModalManager} from './utils/ModalManager.js';

const modalManager = new ModalManager();
// Gestión de Grupos
document.addEventListener('DOMContentLoaded', function () {
    loadGrupos();
    loadMateriasSelect();
    loadProfesoresSelect();

    const formGrupo = document.getElementById('formGrupo');
    if (formGrupo) {
        formGrupo.addEventListener('submit', handleSubmitGrupo);
    }
});

async function loadGrupos() {
    const tbody = document.getElementById('gruposTableBody');

    try {
        const response = await fetch('../php/grupos_api.php?action=list');
        const data = await response.json();

        if (data.success) {
            renderGrupos(data.data);
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Error al cargar grupos</td></tr>';
        }
    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Error al cargar grupos</td></tr>';
    }
}

function renderGrupos(grupos) {
    const tbody = document.getElementById('gruposTableBody');

    if (grupos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron grupos</td></tr>';
        return;
    }

    tbody.innerHTML = grupos.map(grupo => `
        <tr>
            <td><strong>${grupo.nombre}</strong></td>
            <td>${grupo.materia_nombre}</td>
         <td>
            <div class="badge ${grupo.profesor_nombre ? 'badge-info' : 'badge-error'}">
                ${grupo.profesor_nombre ? grupo.profesor_nombre : 'Sin asignar'}
            </div>
        </td>
            <td>${grupo.num_estudiantes} / ${grupo.cupo_maximo}</td>
            <td>${grupo.periodo_academico}</td>
            <td>
                <div class="flex gap-1">
                    <button class="btn btn-sm btn-secondary" onclick="editGrupo(${grupo.id})" title="Editar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteGrupo(${grupo.id})" title="Eliminar">
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

async function loadMateriasSelect() {
    try {
        const response = await fetch('../php/materias_api.php?action=list');
        const data = await response.json();

        if (data.success) {
            const select = document.getElementById('materia_id');
            data.data.forEach(materia => {
                const option = document.createElement('option');
                option.value = materia.id;
                option.textContent = `${materia.nombre} (${materia.codigo})`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function loadProfesoresSelect() {
    try {
        const response = await fetch('../php/docentes_api.php?action=list&activo=1');
        const data = await response.json();

        if (data.success) {
            const select = document.getElementById('profesor_id');
            data.data.forEach(profesor => {
                const option = document.createElement('option');
                option.value = profesor.id;
                option.textContent = `${profesor.nombre} ${profesor.apellido}`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function handleSubmitGrupo(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const grupo_id = formData.get('grupo_id');
    const action = grupo_id ? 'update' : 'create';
    formData.set('action', action);

    modalManager.openInfo(
        'Confirmar Acción',
        `¿Estás seguro de que deseas ${action === 'create' ? 'crear' : 'actualizar'} este grupo?`,
        async () => {
            await confirmSubmitGrupo(formData, form, action);
            modalManager.closeModal(ModalManager.ModalType.INFO);
        });
}

async function confirmSubmitGrupo(formData, form, action) {

    try {
        showLoading(true);
        const response = await fetch('../php/grupos_api.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        showLoading(false);
        if (data.success) {
            notyf.success(data.message);
            closeModal('modalGrupo'); // Modal Close Grupo
            modalManager.openSuccess( // Modal Success
                'Operación Exitosa',
                `El grupo ha sido ${action === 'create' ? 'creado' : 'actualizado'} exitosamente.`,
                () => {
                    form.reset();
                    loadGrupos();
                    modalManager.closeModalPop();
                });

        } else {
            notyf.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        notyf.error('Error al guardar el grupo');
    } finally {
        showLoading(false);
    }
}

window.editGrupo = async (id) => {
    try {
        showLoading(true);
        const response = await fetch(`../php/grupos_api.php?action=get&id=${id}`);
        const data = await response.json();
        showLoading(false);
        if (data.success) {
            const grupo = data.data;

            document.getElementById('grupo_id').value = grupo.id;
            document.getElementById('materia_id').value = grupo.materia_id;
            document.getElementById('profesor_id').value = grupo.profesor_id;
            document.getElementById('nombre').value = grupo.nombre;
            document.getElementById('cupo_maximo').value = grupo.cupo_maximo;
            document.getElementById('semestre_actual').value = grupo.semestre_actual;
            document.getElementById('periodo_academico').value = grupo.periodo_academico;

            document.getElementById('modalGrupoTitle').textContent = 'Editar Grupo';
            openModal('modalGrupo');
        } else {
            notyf.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        notyf.error('Error al cargar el grupo');
    } finally {
        showLoading(false);
    }
}

window.deleteGrupo = async (id) => {
    modalManager.openWarning(
        '¿Estás seguro de que deseas eliminar este grupo?',
        'Esta acción no se puede deshacer.',
        async () => {
            await confirmDeleteGrupo(id);
            modalManager.closeModalPop();
        });
};

async function confirmDeleteGrupo(id) {
    try {
        showLoading(true);
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        const response = await fetch('../php/grupos_api.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        showLoading(false);

        if (data.success) {
            notyf.success(data.message);
            await loadGrupos();
        } else {
            notyf.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        notyf.error('Error al eliminar el grupo')
    } finally {
        showLoading(false);
    }
}
