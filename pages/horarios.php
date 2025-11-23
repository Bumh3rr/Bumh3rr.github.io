<?php
require_once '../php/config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Horarios</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <div class="dashboard">
        <?php include './components/sidebar.php'; ?>

        <div class="main-content">
            <div class="topbar">
                <h1 class="topbar-title">Gestión de Horarios</h1>
                <div class="topbar-actions">
                    <button class="btn btn-primary" onclick="openModal('modalHorario')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Asignar Horario
                    </button>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Filtros -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="grid grid-3">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Carrera</label>
                                <select id="filterCarrera" class="form-input">
                                    <option value="">Todas las carreras</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Semestre</label>
                                <select id="filterSemestre" class="form-input">
                                    <option value="">Todos los semestres</option>
                                    <option value="1">1er Semestre</option>
                                    <option value="2">2do Semestre</option>
                                    <option value="3">3er Semestre</option>
                                    <option value="4">4to Semestre</option>
                                    <option value="5">5to Semestre</option>
                                    <option value="6">6to Semestre</option>
                                    <option value="7">7mo Semestre</option>
                                    <option value="8">8vo Semestre</option>
                                    <option value="9">9no Semestre</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Grupo</label>
                                <select id="filterGrupo" class="form-input">
                                    <option value="">Todos los grupos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vista de Horario -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Horario Semanal</h2>
                    </div>
                    <div class="card-body">
                        <div id="scheduleContainer">
                            <div class="schedule-grid">
                                <div class="schedule-header">Hora</div>
                                <div class="schedule-header">Lunes</div>
                                <div class="schedule-header">Martes</div>
                                <div class="schedule-header">Miércoles</div>
                                <div class="schedule-header">Jueves</div>
                                <div class="schedule-header">Viernes</div>

                                <!-- Horarios de 7:00 a 21:00 -->
                                <?php
                                $horas = [
                                    '07:00 - 08:00', '08:00 - 09:00', '09:00 - 10:00', '10:00 - 11:00',
                                    '11:00 - 12:00', '12:00 - 13:00', '13:00 - 14:00', '14:00 - 15:00'
                                ];
                                $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                                
                                foreach ($horas as $hora) {
                                    echo "<div class='schedule-time'>{$hora}</div>";
                                    foreach ($dias as $dia) {
                                        $horaInicio = explode(' - ', $hora)[0];
                                        echo "<div class='schedule-cell' data-dia='{$dia}' data-hora='{$horaInicio}'></div>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Horarios -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h2 class="card-title">Lista de Horarios</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Grupo</th>
                                        <th>Materia</th>
                                        <th>Profesor</th>
                                        <th>Aula</th>
                                        <th>Día</th>
                                        <th>Horario</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="horariosTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">Cargando...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Horario -->
    <div id="modalHorario" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Asignar Horario</h3>
                <button class="modal-close" onclick="closeModal('modalHorario')">&times;</button>
            </div>
            <form id="formHorario">
                <div class="modal-body">
                    <input type="hidden" id="horario_id" name="horario_id">
                    
                    <div class="form-group">
                        <label class="form-label">Grupo</label>
                        <select id="grupo_id" name="grupo_id" class="form-input" required>
                            <option value="">Seleccionar grupo...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Aula</label>
                        <select id="aula_id" name="aula_id" class="form-input" required>
                            <option value="">Seleccionar aula...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Día de la semana</label>
                        <select id="dia_semana" name="dia_semana" class="form-input" required>
                            <option value="">Seleccionar día...</option>
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miércoles">Miércoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                        </select>
                    </div>

                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Hora inicio</label>
                            <input type="time" id="hora_inicio" name="hora_inicio" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hora fin</label>
                            <input type="time" id="hora_fin" name="hora_fin" class="form-input" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalHorario')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/main.js"></script>
    <script src="../js/horarios.js"></script>
</body>
</html>
