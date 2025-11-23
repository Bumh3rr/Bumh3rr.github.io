<?php
require_once '../php/config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Docentes</title>
    <link rel="stylesheet" href="../css/styles-modal.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
</head>
<body>
<div class="dashboard">
    <?php include './components/sidebar.php'; ?>
    <?php include './components/loading.php'; ?>
    <?php include './components/modal-success.php'; ?>
    <?php include './components/modal-warning.php'; ?>
    <?php include './components/modal-info.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <h1 class="topbar-title">Gestión de Docentes</h1>
            <div class="topbar-actions">
                <button class="btn btn-primary" onclick="openModal('modalDocente')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Nuevo Docente
                </button>
            </div>
        </div>

        <div class="content-wrapper">
            <div id="alertContainer"></div>

            <!-- Filtros -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="flex gap-2">
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <input type="text" id="searchInput" class="form-input" placeholder="Buscar por nombre, email o RFC...">
                        </div>
                        <div class="form-group" style="width: 200px; margin-bottom: 0;">
                            <select id="filterActivo" class="form-input">
                                <option value="">Todos</option>
                                <option value="1">Activos</option>
                                <option value="0">Inactivos</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Docentes -->
            <div class="card">
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>RFC</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody id="docentesTableBody">
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

<!-- Modal Docente -->
<div id="modalDocente" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="content-title-modal">
                <div class="ico-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28"><path fill="#5046e5" d="M5.75 2A2.75 2.75 0 0 0 3 4.75V6.5h-.75a.75.75 0 0 0 0 1.5H3v4.25h-.75a.75.75 0 0 0 0 1.5H3V18h-.75a.75.75 0 0 0 0 1.5H3v1.75A2.75 2.75 0 0 0 5.75 24h6.346a14 14 0 0 1-.717-1.5H5.75c-.69 0-1.25-.56-1.25-1.25V19.5h.75a.75.75 0 0 0 0-1.5H4.5v-4.25h.75a.75.75 0 0 0 0-1.5H4.5V8h.75a.75.75 0 0 0 0-1.5H4.5V4.75c0-.69.56-1.25 1.25-1.25h11.5c.69 0 1.25.56 1.25 1.25v3.256q.334.228.631.502a6 6 0 0 1 .869-.76V4.75A2.75 2.75 0 0 0 17.25 2zm16.684 6.442a.75.75 0 0 0-.992-.376a5 5 0 0 0-2.256 2.025A4.75 4.75 0 0 0 15.25 8h-.75a1 1 0 0 0-1 1v.75c0 1.343.557 2.556 1.453 3.42c-1.76.508-3.453 2.17-3.453 5.044c0 3.608 2.14 6.562 3.22 7.833c.554.65 1.357.953 2.146.953c.877 0 1.712-.372 2.3-1.023l.084-.094l.084.094a3.1 3.1 0 0 0 2.3 1.023c.79 0 1.592-.302 2.145-.953C24.861 24.776 27 21.822 27 18.214C27 14.7 24.47 13 22.381 13a7.4 7.4 0 0 0-2.434.398q.02-.073.032-.148H20v-.633a3.49 3.49 0 0 1 2.058-3.183a.75.75 0 0 0 .376-.992M15.25 9.5a3.25 3.25 0 0 1 3.25 3.25V13h-.25A3.25 3.25 0 0 1 15 9.75V9.5zM13 18.214c0-2.64 1.813-3.714 3.119-3.714a5.9 5.9 0 0 1 2.008.338c.261.096.56.187.881.23q.241.033.484 0c.32-.043.62-.134.881-.23a5.9 5.9 0 0 1 2.008-.338c1.306 0 3.119 1.073 3.119 3.714c0 3.055-1.84 5.66-2.863 6.86c-.228.268-.586.426-1.003.426c-.452 0-.883-.192-1.185-.527l-.097-.107a1.486 1.486 0 0 0-2.204 0l-.097.107a1.6 1.6 0 0 1-1.185.527c-.417 0-.775-.157-1.003-.425C14.841 23.873 13 21.269 13 18.215m2.772-2.145a.75.75 0 1 1 .628 1.363a.88.88 0 0 0-.507.912l.101.813a.75.75 0 1 1-1.488.186l-.102-.813a2.38 2.38 0 0 1 1.368-2.461" stroke-width="0.1" stroke="#5046e5"/></svg>                </div>
                <h3 class="modal-title" id="modalDocenteTitle">Nuevo Docente</h3>
            </div>
            <button class="modal-close" onclick="closeModal('modalDocente')">&times;</button>
        </div>
        <form id="formDocente">
            <div class="modal-body">
                <input type="hidden" id="docente_id" name="docente_id">

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Ingresa el nombre completo" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellido</label>
                        <input type="text" id="apellido" name="apellido" class="form-input" placeholder="Ingresa los apellidos" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="Ingresa el correo electrónico" required>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">RFC</label>
                        <input type="text" id="rfc" name="rfc" class="form-input" maxlength="13" placeholder="13 caracteres" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="form-input" placeholder="Ej: 747-111-2222" maxlength="10">
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" class="ui-checkbox" id="activo" name="activo" checked>
                        <span>Docente activo</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalDocente')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Horario Docente -->
<div id="modalHorarioDocente" class="modal">
    <div class="modal-content" style="max-width: 1200px;">
        <div class="modal-header">
            <div class="content-title-modal">
                <div class="ico-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><g fill="none" stroke="#5046e5" stroke-linecap="round" stroke-width="4"><rect width="40" height="30" x="4" y="10" stroke-linejoin="round" rx="2"/><path d="M14 6v8m11 9H14m20 8H14M34 6v8"/></g></svg>                </div>
                <h3 class="modal-title" id="modalHorarioDocenteTitle">Horario del Docente</h3>
            </div>
            <button class="modal-close" onclick="closeModal('modalHorarioDocente')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="docenteInfo" class="mb-3" style="padding: 16px; background: var(--background); border-radius: 8px;">
                <!-- Info del docente se carga dinámicamente -->
            </div>

            <!-- Grid de Horario -->
            <div class="schedule-grid" id="scheduleGridDocente">
                <div class="schedule-header">Hora</div>
                <div class="schedule-header">Lunes</div>
                <div class="schedule-header">Martes</div>
                <div class="schedule-header">Miércoles</div>
                <div class="schedule-header">Jueves</div>
                <div class="schedule-header">Viernes</div>
                <div class="schedule-header">Sábado</div>

                <!-- Horarios de 7:00 AM a 3:00 PM (8 bloques) -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalHorarioDocente')">Cerrar</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script  src="../js/main.js"></script>
<script type="module" src="../js/docentes.js"></script>
</body>
</html>