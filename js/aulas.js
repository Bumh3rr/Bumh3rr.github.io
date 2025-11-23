import {showLoading, notyf} from './notify/Config.js';

// Gestión de Aulas
document.addEventListener('DOMContentLoaded', function() {
    loadAulas();

    // Búsqueda
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            loadAulas();
        }, 550));
    }

    // Filtro por tipo
    const filterTipo = document.getElementById('filterTipo');
    if (filterTipo) {
        filterTipo.addEventListener('change', function() {
            loadAulas();
        });
    }

    // Formulario de aula
    const formAula = document.getElementById('formAula');
    if (formAula) {
        formAula.addEventListener('submit', handleSubmitAula);
    }
});

// Cargar aulas
async function loadAulas() {
    const tbody = document.getElementById('aulasTableBody');
    const search = document.getElementById('searchInput')?.value || '';
    const tipo = document.getElementById('filterTipo')?.value || '';

    try {
        showLoading(true);
        const response = await fetch(`../php/aulas_api.php?action=list&search=${encodeURIComponent(search)}&tipo=${tipo}`);
        const data = await response.json();
        showLoading(false);

        if (data.success) {
            renderAulas(data.data);
        } else {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">Error al cargar aulas</td></tr>';
        }
    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Error al cargar aulas</td></tr>';
    }finally {
        showLoading(false);
    }
}

// Renderizar aulas en tabla
function renderAulas(aulas) {
    const tbody = document.getElementById('aulasTableBody');

    if (aulas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron aulas</td></tr>';
        return;
    }

    const tipoColors = {
        'teorica': 'primary',
        'laboratorio': 'warning',
        'auditorio': 'warning',
        'taller': 'success',
        'redes': 'error',
        'computacion': 'info',
        'software': 'info'
    };

    const tipoNames = {
        'teorica': 'Teórica',
        'laboratorio': 'Laboratorio',
        'auditorio': 'Auditorio',
        'taller': 'Taller',
        'redes': 'Redes',
        'computacion': 'Computación',
        'software': 'Software'
    };

    tbody.innerHTML = aulas.map(aula => `
        <tr>
            <td><strong>${aula.nombre}</strong></td>
            <td>${aula.edificio}</td>
            <td>${aula.capacidad} personas</td>
            <td><span class="badge badge-${tipoColors[aula.tipo]}">${tipoNames[aula.tipo]}</span></td>
            <td>
                <span class="badge badge-${aula.activo == 1 ? 'success' : 'error'}">
                    ${aula.activo == 1 ? 'Activa' : 'Inactiva'}
                </span>
            </td>
            <td>
                <div class="flex gap-1">
                    <button class="btn btn-sm btn-secondary" onclick="editAula(${aula.id})" title="Editar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Manejar envío de formulario
async function handleSubmitAula(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const aula_id = formData.get('aula_id');
    const action = aula_id ? 'update' : 'create';

    // Agregar activo como checkbox
    formData.set('activo', document.getElementById('activo').checked ? '1' : '0');
    formData.set('action', action);

    try {
        showLoading(true);
        const response = await fetch('../php/aulas_api.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        showLoading(false);

        if (data.success) {
            notyf.success(data.message);
            closeModal('modalAula');
            await loadAulas();
            form.reset();
        } else {
            notyf.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        notyf.error('Error al guardar el aula');
    }finally {
        showLoading(false);
    }
}

// Editar aula
window.editAula = async (id) => {
    try {
        showLoading(true);
        const response = await fetch(`../php/aulas_api.php?action=get&id=${id}`);
        const data = await response.json();
        showLoading(false);

        if (data.success) {
            const aula = data.data;

            document.getElementById('aula_id').value = aula.id;
            document.getElementById('nombre').value = aula.nombre;
            document.getElementById('edificio').value = aula.edificio;
            document.getElementById('capacidad').value = aula.capacidad;
            document.getElementById('tipo').value = aula.tipo;
            document.getElementById('activo').checked = aula.activo == 1;

            document.getElementById('modalAulaTitle').textContent = 'Editar Aula';
            openModal('modalAula');
        } else {
            notyf.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        notyf.error('Error al cargar el aula');
    }finally {
        showLoading(false);
    }
}
