// Mi Horario - Estudiante/Profesor
document.addEventListener('DOMContentLoaded', function() {
    loadMySchedule();
    loadMyClases();
});

// Cargar mi horario visual
async function loadMySchedule() {
    try {
        const response = await fetch('../../php/horarios_api.php?action=my_schedule');
        const data = await response.json();

        if (data.success) {
            renderMySchedule(data.data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Renderizar mi horario visual
function renderMySchedule(horarios) {
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
                        ${horario.aula_nombre}${horario.aula_edificio ? ' (' + horario.aula_edificio + ')' : ''}<br>
                        ${horario.profesor_nombre || ''}
                    </div>
                </div>
            `;
        });
    });
}

// Cargar lista de clases
async function loadMyClases() {
    const tbody = document.getElementById('clasesTableBody');

    try {
        const response = await fetch('../../php/horarios_api.php?action=my_schedule');
        const data = await response.json();

        if (data.success) {
            renderMyClases(data.data);
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Error al cargar las clases</td></tr>';
        }
    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Error al cargar las clases</td></tr>';
    }
}

// Renderizar lista de clases
function renderMyClases(clases) {
    const tbody = document.getElementById('clasesTableBody');

    if (clases.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No tienes clases asignadas</td></tr>';
        return;
    }

    // Ordenar por día y hora
    const diasOrden = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    clases.sort((a, b) => {
        const diaCompare = diasOrden.indexOf(a.dia_semana) - diasOrden.indexOf(b.dia_semana);
        if (diaCompare !== 0) return diaCompare;
        return a.hora_inicio.localeCompare(b.hora_inicio);
    });

    tbody.innerHTML = clases.map(clase => `
        <tr>
            <td><strong>${clase.materia_nombre}</strong></td>
            <td>${clase.grupo_nombre}</td>
            <td>${clase.profesor_nombre || '-'}</td>
            <td>${clase.aula_nombre}${clase.aula_edificio ? ' (' + clase.aula_edificio + ')' : ''}</td>
            <td>${clase.dia_semana}</td>
            <td>${formatTime(clase.hora_inicio)} - ${formatTime(clase.hora_fin)}</td>
        </tr>
    `).join('');
}
