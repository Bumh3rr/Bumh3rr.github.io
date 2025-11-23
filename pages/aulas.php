<?php
require_once '../php/config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Aulas</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
</head>
<body>
<div class="dashboard">
    <?php include './components/sidebar.php'; ?>
    <?php include './components/loading.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <h1 class="topbar-title">Gestión de Aulas</h1>
            <div class="topbar-actions">
                <button class="btn btn-primary" onclick="openModal('modalAula')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Nueva Aula
                </button>
            </div>
        </div>

        <div class="content-wrapper">
            <div id="alertContainer"></div>

            <!-- Filtros -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="grid grid-2">
                        <div class="form-group" style="margin-bottom: 0;">
                            <input type="text" id="searchInput" class="form-input" placeholder="Buscar por nombre o edificio...">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <select id="filterTipo" class="form-input">
                                <option value="">Todos los tipos</option>
                                <option value="teorica">Teórica</option>
                                <option value="laboratorio">Laboratorio</option>
                                <option value="computacion">Computación</option>
                                <option value="auditorio">Auditorio</option>
                                <option value="software">Software</option>
                                <option value="taller">Taller</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Aulas -->
            <div class="card">
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Edificio</th>
                                <th>Capacidad</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody id="aulasTableBody">
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

<!-- Modal Aula -->
<div id="modalAula" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalAulaTitle">Nueva Aula</h3>
            <button class="modal-close" onclick="closeModal('modalAula')">&times;</button>
        </div>
        <form id="formAula">
            <div class="modal-body">
                <input type="hidden" id="aula_id" name="aula_id">

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Nombre del Aula</label>
                        <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Ej: Aula 101" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Edificio</label>
                        <input type="text" id="edificio" name="edificio" class="form-input" placeholder="Ej: Edificio A" required>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Capacidad</label>
                        <input type="number" id="capacidad" name="capacidad" class="form-input" min="1" placeholder="Ej: 30" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipo</label>
                        <select id="tipo" name="tipo" class="form-input" required>
                            <option value="">Seleccionar...</option>
                            <option value="teorica">Teórica</option>
                            <option value="laboratorio">Laboratorio</option>
                            <option value="computacion">Computación</option>
                            <option value="auditorio">Auditorio</option>
                            <option value="software">Software</option>
                            <option value="taller">Taller</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" class="ui-checkbox" id="activo" name="activo" checked>
                        <span>Aula activa</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAula')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="../js/main.js"></script>
<script type="module" src="../js/aulas.js"></script>
</body>
</html>