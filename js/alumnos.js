import {showLoading, notyf} from './notify/Config.js';
import {ModalManager} from './utils/ModalManager.js';

const modalManager = new ModalManager();

document.addEventListener('DOMContentLoaded', function () {
    loadMaterias();

    // Búsqueda
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function () {
            loadMaterias();
        }, 550));
    }

    // Filtros
    const filterCarrera = document.getElementById('filterCarrera');
    const filterSemestre = document.getElementById('filterSemestre');

    if (filterCarrera) {
        filterCarrera.addEventListener('change', function () {
            loadMaterias();
        });
    }

    if (filterSemestre) {
        filterSemestre.addEventListener('change', function () {
            loadMaterias();
        });
    }

    // Formulario de materia
    const formMateria = document.getElementById('formAgregarAlumnos');
    if (formMateria) {
        formMateria.addEventListener('submit', handleSubmitMateria);
    }
});

// Cargar materias
async function loadMaterias() {
    const tbody = document.getElementById('materiasTableBody');
    const search = document.getElementById('searchInput')?.value || '';
    const carrera = document.getElementById('filterCarrera')?.value || '';
    const semestre = document.getElementById('filterSemestre')?.value || '';

    try {
        showLoading(true);
        const response = await fetch(`../php/materias_api.php?action=list&search=${encodeURIComponent(search)}&carrera=${carrera}&semestre=${semestre}`);
        const data = await response.json();
        showLoading(false);

        if (data.success) {
            renderMaterias(data.data);
        } else {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">Error al cargar materias</td></tr>';
        }
    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Error al cargar materias</td></tr>';
    } finally {
        showLoading(false);
    }
}

// Renderizar materias en tabla
function renderMaterias(materias) {
    const tbody = document.getElementById('materiasTableBody');

    if (materias.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron materias</td></tr>';
        return;
    }

    tbody.innerHTML = materias.map(materia => `
        <tr>
            <td><span class="badge badge-primary">${materia.codigo}</span></td>
            <td><strong>${materia.nombre}</strong></td>
            <td>${materia.carrera_nombre}</td>
            <td>${materia.semestre}° Sem</td>
            <td>${materia.creditos}</td>
            <td>${materia.horas_semanales} hrs</td>
            <td>
                <span class="badge badge-${materia.alumnos_inscriptos == 1 ? 'success' : 'error'}">
                    ${materia.alumnos_inscriptos == 1 ? 'Alumnos Inscriptos' : 'No hay Alumnos Inscriptos'}
                </span>
            </td>
            <td>
                <button class="btn btn-primary" onclick="agregarAlumnos(${materia.id})" title="agregar"  ${materia.alumnos_inscriptos === 1 ? 'disabled' : ''}>
                    <div style="display: flex; flex-flow: row wrap; align-items: center; justify-content: center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><g fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M15 8A5 5 0 1 0 5 8a5 5 0 0 0 10 0m2.5 13v-7M14 17.5h7"/><path d="M3 20a7 7 0 0 1 11-5.745"/></g></svg>
                            <p style="font-size: 11px;">Agregar Alumnos</p>
                    </div>
                </button>
            </td>
        </tr>
    `).join('');
}

// Manejar envío de formulario
async function handleSubmitMateria(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const action =  'create';

    // Validaciones
    const numero_alumnos = parseInt(formData.get('numero_alumnos'));
    const min_alumnos = parseInt(formData.get('min_alumnos'));
    const periodo_academico = formData.get('periodo_academico');

    if (numero_alumnos < min_alumnos || numero_alumnos > 200) {
        notyf.error(`El número de alumnos debe estar entre ${min_alumnos} y 200`);
        return;
    }

    if (!periodo_academico || periodo_academico.trim() === '') {
        notyf.error('El periodo académico es obligatorio');
        return;
    }
    formData.set('action', action);

    modalManager.openInfo(
        'Confirmar Acción',
        '¿Estás seguro de que deseas guardar estos cambios?',
        async () => {
            await save(formData, form, action);
            modalManager.closeModal(ModalManager.ModalType.INFO);
        });
}

async function save(formData, form, action) {
    try {
        showLoading(true);
        const response = await fetch(`../php/alumnos_api.php?action=${action}`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        showLoading(false);

        if (data.success) {
            notyf.success(data.message);
            closeModal('modalAgregarAlumnos');
            await loadMaterias();
            form.reset();
            mostrarModalGruposGenerados(data);
        } else {
            notyf.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        notyf.error('Error al guardar la materia');
    } finally {
        showLoading(false);
    }
}

// JavaScript
function escapeHtml(str) {
    if (str === undefined || str === null) return '';
    return String(str).replace(/[&<>"'`=\/]/g, function (s) {
        return ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
            '/': '&#x2F;',
            '`': '&#x60;',
            '=': '&#x3D;'
        })[s];
    });
}

function renderGeneratedGroups(response) {
    const container = document.getElementById('info-generated-content');
    if (!container) return;

    const created = (response && response.data && response.data.grupos_creados) || [];
    const omitted = (response && response.data && response.data.grupos_omitidos) || [];

    let html = '';

    html += `<div class="generated-summary" style="display:flex;flex-direction:column;gap:16px;">`;
    html += `<div style="display:flex;justify-content:space-between;align-items:center;">`;
    html += `<div><strong>Grupos creados:</strong> ${created.length}</div>`;
    html += `<div style="color:#b45309;"><strong>Omitidos:</strong> ${omitted.length}</div>`;
    html += `</div>`;

    if (created.length > 0) {
        html += `<div class="created-groups">`;
        html += `<h4 style="margin:8px 0 6px 0;">Grupos creados</h4>`;
        html += `<div style="display:flex;flex-wrap:wrap;gap:12px;">`;
        created.forEach(g => {
            html += `
                <div class="group-card" style="border:1px solid #e6e6f7;border-radius:8px;padding:12px;width:220px;background:#fff;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <div style="font-weight:600;font-size:14px;">${escapeHtml(g.nombre_grupo)}</div>
                        <div style="font-size:12px;color:#6b7280;">ID: ${escapeHtml(g.grupo_id)}</div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div style="font-size:13px;color:#374151;">Alumnos:</div>
                        <div style="background:#eef2ff;color:#4f46e5;padding:4px 8px;border-radius:999px;font-weight:600;">${escapeHtml(g.alumnos_inscriptos)}</div>
                    </div>
                </div>
            `;
        });
        html += `</div></div>`;
    } else {
        html += `<div style="color:#6b7280;">No se crearon grupos.</div>`;
    }

    if (omitted.length > 0) {
        html += `<div class="omitted-groups" style="margin-top:8px;">`;
        html += `<h4 style="margin:8px 0 6px 0;color:#92400e;">Grupos omitidos</h4>`;
        html += `<div style="overflow:auto;"><table style="width:100%;border-collapse:collapse;">`;
        html += `<thead><tr style="background:#fff7ed;color:#92400e;text-align:left;"><th style="padding:8px;border-bottom:1px solid #f3e8ff;">Grupo</th><th style="padding:8px;border-bottom:1px solid #f3e8ff;">Alumnos</th><th style="padding:8px;border-bottom:1px solid #f3e8ff;">Motivo</th></tr></thead><tbody>`;
        omitted.forEach(o => {
            html += `<tr><td style="padding:8px;border-bottom:1px solid #f3f4f6;">${escapeHtml(o.nombre_grupo)}</td><td style="padding:8px;border-bottom:1px solid #f3f4f6;">${escapeHtml(o.alumnos_inscriptos)}</td><td style="padding:8px;border-bottom:1px solid #f3f4f6;color:#92400e">${escapeHtml(o.motivo)}</td></tr>`;
        });
        html += `</tbody></table></div>`;
        html += `</div>`;
    }

    html += `</div>`; // cierre generated-summary

    container.innerHTML = html;
}

// Reemplaza la función actual por esta para mostrar datos y abrir el modal
function mostrarModalGruposGenerados(response) {
    renderGeneratedGroups(response);
    openModal('modal-info-generated');
}

// Editar materia
window.agregarAlumnos = async (id) => {
    try {
        showLoading(true);
        const response = await fetch(`../php/materias_api.php?action=get&id=${id}`);
        const data = await response.json();

        showLoading(false);
        if (data.success) {
            const materia = data.data;

            document.getElementById('materia_id').value = materia.id;
            document.getElementById('semestre_actual').value = materia.semestre;

            openModal('modalAgregarAlumnos');
        } else {
            notyf.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        notyf.error('Error al cargar la materia');
    } finally {
        showLoading(false);
    }
}
