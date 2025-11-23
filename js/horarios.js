// Gestión de Horarios
document.addEventListener('DOMContentLoaded', function() {
    loadCarreras();
    loadGruposSelect();
    loadAulasSelect();
    loadHorarios();

    // Filtros
    const filterCarrera = document.getElementById('filterCarrera');
    const filterSemestre = document.getElementById('filterSemestre');
    const filterGrupo = document.getElementById('filterGrupo');

    if (filterCarrera) {
        filterCarrera.addEventListener('change', function() {
            loadSchedule();
            loadHorarios();
        });
    }

    if (filterSemestre) {
        filterSemestre.addEventListener('change', function() {
            loadSchedule();
            loadHorarios();
        });
    }

    if (filterGrupo) {
        filterGrupo.addEventListener('change', function() {
            loadSchedule();
            loadHorarios();
        });
    }

    // Formulario de horario
    const formHorario = document.getElementById('formHorario');
    if (formHorario) {
        formHorario.addEventListener('submit', handleSubmitHorario);
    }
});

// Cargar carreras para el filtro
async function loadCarreras() {
    try {
        const response = await fetch('../../php/carreras_api.php?action=list');
        const data = await response.json();

        if (data.success) {
            const select = document.getElementById('filterCarrera');
            data.data.forEach(carrera => {
                const option = document.createElement('option');
                option.value = carrera.id;
                option.textContent = carrera.nombre;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Cargar grupos para el select
async function loadGruposSelect() {
    try {
        const response = await fetch('../../php/grupos_api.php?action=list');
        const data = await response.json();

        if (data.success) {
            const select = document.getElementById('grupo_id');
            const filterSelect = document.getElementById('filterGrupo');
            
            data.data.forEach(grupo => {
                const option = document.createElement('option');
                option.value = grupo.id;
                option.textContent = `${grupo.nombre} - ${grupo.materia_nombre}`;
                select.appendChild(option.cloneNode(true));
                if (filterSelect) filterSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Cargar aulas para el select
async function loadAulasSelect() {
    try {
        const response = await fetch('../../php/aulas_api.php?action=list');
        const data = await response.json();

        if (data.success) {
            const select = document.getElementById('aula_id');
            data.data.forEach(aula => {
                const option = document.createElement('option');
                option.value = aula.id;
                option.textContent = `${aula.nombre} (${aula.edificio}) - Cap: ${aula.capacidad}`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Cargar horarios en tabla
async function loadHorarios() {
    const tbody = document.getElementById('horariosTableBody');
    const grupo = document.getElementById('filterGrupo')?.value || '';

    try {
        const response = await fetch(`../php/horarios_api.php?action=list&grupo=${grupo}`);
        const data = await response.json();

        if (data.success) {
            renderHorarios(data.data);
        } else {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">Error al cargar horarios</td></tr>';
        }
    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Error al cargar horarios</td></tr>';
    }
}

// Renderizar horarios en tabla
function renderHorarios(horarios) {
    const tbody = document.getElementById('horariosTableBody');

    if (horarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron horarios</td></tr>';
        return;
    }

    tbody.innerHTML = horarios.map(horario => `
        <tr>
            <td><strong>${horario.grupo_nombre}</strong></td>
            <td>${horario.materia_nombre}</td>
            <td>${horario.profesor_nombre}</td>
            <td>${horario.aula_nombre} (${horario.aula_edificio})</td>
            <td>${horario.dia_semana}</td>
            <td>${formatTime(horario.hora_inicio)} - ${formatTime(horario.hora_fin)}</td>
            <td>
                <div class="flex gap-1">
                    <button class="btn btn-sm btn-danger" onclick="deleteHorario(${horario.id})" title="Eliminar">
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

// Cargar horario visual
async function loadSchedule() {
    const grupo = document.getElementById('filterGrupo')?.value || '';

    try {
        console.log('Cargando horario para el grupo:', grupo);
        const response = await fetch(`../php/horarios_api.php?action=schedule&grupo=${grupo}`);
        const data = await response.json();

        if (data.success) {
            const horarios = data.data.map(h => {
                return {
                    ...h,
                    hora_inicio: (h.hora_inicio || '').slice(0,5),
                    hora_fin: (h.hora_fin || '').slice(0,5)
                };
            });
            renderSchedule(horarios);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Renderizar horario visual
function renderSchedule(horarios) {
    // Limpiar todas las celdas
    document.querySelectorAll('.schedule-cell').forEach(cell => {
        cell.innerHTML = '';
        cell.style.backgroundColor = '';
    });

    // Renderizar cada horario
    horarios.forEach(horario => {
        const cells = document.querySelectorAll(`[data-dia="${horario.dia_semana}"][data-hora="${horario.hora_inicio}"]`);
        
        cells.forEach(cell => {
            cell.innerHTML = `
                <div class="schedule-class">
                    <div class="schedule-class-name">${horario.materia_nombre}</div>
                    <div class="schedule-class-info">
                        ${horario.grupo_nombre}<br>
                        ${horario.aula_nombre}<br>
                        ${horario.profesor_nombre}
                    </div>
                </div>
            `;
        });
    });
}

// Manejar envío de formulario
async function handleSubmitHorario(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    
    const hora_inicio = formData.get('hora_inicio');
    const hora_fin = formData.get('hora_fin');

    // Validar que hora_fin sea mayor que hora_inicio
    if (hora_inicio >= hora_fin) {
        showAlert('La hora de fin debe ser mayor que la hora de inicio', 'error');
        return;
    }

    formData.set('action', 'create');

    try {
        const response = await fetch('../../php/horarios_api.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showAlert(data.message, 'success');
            closeModal('modalHorario');
            loadHorarios();
            loadSchedule();
            form.reset();
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al guardar el horario', 'error');
    }
}

// Eliminar horario
async function deleteHorario(id) {
    if (!confirmDelete('¿Estás seguro de que deseas eliminar este horario?')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        const response = await fetch('../../php/horarios_api.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showAlert(data.message, 'success');
            loadHorarios();
            loadSchedule();
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al eliminar el horario', 'error');
    }
}
