<?php
require_once '../php/config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Grupos</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/styles-modal.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

</head>
<body>
<div class="dashboard">
    <?php include './components/sidebar.php'; ?>
    <?php include './components/loading.php'; ?>
    <?php include './components/modal-success.php'; ?>
    <?php include './components/modal-info.php'; ?>
    <?php include './components/modal-warning.php'; ?>
    <?php include './components/modal-error.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <h1 class="topbar-title">Gestión de Grupos</h1>
            <div class="topbar-actions">
                <button class="btn btn-primary" onclick="openModal('modalGrupo')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Nuevo Grupo
                </button>
            </div>
        </div>

        <div class="content-wrapper">
            <div id="alertContainer"></div>

            <div class="card">
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Materia</th>
                                <th>Profesor</th>
                                <th>Estudiantes</th>
                                <th>Periodo</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody id="gruposTableBody">
                            <tr>
                                <td colspan="6" class="text-center">Cargando...</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Grupo -->
<div id="modalGrupo" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="content-title-modal">
                <div class="ico-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 256 256">
                        <path fill="#5046e5"
                              d="M28.4 124.8a6 6 0 0 0 8.4-1.2a54 54 0 0 1 86.4 0a6 6 0 0 0 8.4 1.19a5.6 5.6 0 0 0 1.19-1.19a54 54 0 0 1 86.4 0a6 6 0 0 0 9.6-7.21a65.74 65.74 0 0 0-29.69-22.26a38 38 0 1 0-46.22 0A65.3 65.3 0 0 0 128 110.7a65.3 65.3 0 0 0-24.89-16.57a38 38 0 1 0-46.22 0A65.7 65.7 0 0 0 27.2 116.4a6 6 0 0 0 1.2 8.4M176 38a26 26 0 1 1-26 26a26 26 0 0 1 26-26m-96 0a26 26 0 1 1-26 26a26 26 0 0 1 26-26m119.11 160.13a38 38 0 1 0-46.22 0A65.3 65.3 0 0 0 128 214.7a65.3 65.3 0 0 0-24.89-16.57a38 38 0 1 0-46.22 0A65.7 65.7 0 0 0 27.2 220.4a6 6 0 1 0 9.6 7.2a54 54 0 0 1 86.4 0a6 6 0 0 0 8.4 1.19a5.6 5.6 0 0 0 1.19-1.19a54 54 0 0 1 86.4 0a6 6 0 0 0 9.6-7.21a65.74 65.74 0 0 0-29.68-22.26M80 142a26 26 0 1 1-26 26a26 26 0 0 1 26-26m96 0a26 26 0 1 1-26 26a26 26 0 0 1 26-26"
                              stroke-width="6.5" stroke="#5046e5"/>
                    </svg>
                </div>
                <h3 class="modal-title" id="modalGrupoTitle">Nuevo Grupo</h3>
            </div>
            <button class="modal-close" onclick="closeModal('modalGrupo')">&times;</button>
        </div>

        <form id="formGrupo">
            <div class="modal-body">
                <input type="hidden" id="grupo_id" name="grupo_id">

                <div class="form-group">
                    <label class="form-label">Materia</label>
                    <select id="materia_id" name="materia_id" class="form-input" required>
                        <option value="">Seleccionar...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Profesor</label>
                    <select id="profesor_id" name="profesor_id" class="form-input" required>
                        <option value="">Seleccionar...</option>
                    </select>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Nombre del Grupo</label>
                        <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Ej: POO-A"
                               required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cupo Máximo</label>
                        <input type="number" id="cupo_maximo" name="cupo_maximo" class="form-input" min="1" max="30"
                               placeholder="Máximo: 30 alumnos" required>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Semestre</label>
                        <input type="number" id="semestre_actual" name="semestre_actual" class="form-input" min="1"
                               max="9" placeholder="Ej: 3" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Periodo Académico</label>
                        <input type="text" id="periodo_academico" name="periodo_academico" class="form-input"
                               placeholder="Ej: Agosto-Diciembre 2024" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="num_alumnos" class="form-label">Numero de Alumnos</label>
                    <input type="number" id="num_alumnos" name="num_alumnos" class="form-input" placeholder="Ej: 23" required>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalGrupo')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="../js/main.js"></script>
<script type="module" src="../js/grupos.js"></script>
</body>
</html>
