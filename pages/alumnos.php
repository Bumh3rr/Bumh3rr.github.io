<?php
require_once '../php/config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Materias</title>
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
            <h1 class="topbar-title">Registro de Alumnos</h1>
        </div>

        <div class="content-wrapper">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="grid grid-3">
                        <div class="form-group" style="margin-bottom: 0;">
                            <input type="text" id="searchInput" class="form-input"
                                   placeholder="Buscar por nombre o código...">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <select id="filterCarrera" class="form-input">
                                <option value="">Todas las carreras</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
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
                    </div>
                </div>
            </div>

            <!-- Tabla de Materias -->
            <div class="card">
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Carrera</th>
                                <th>Semestre</th>
                                <th>Créditos</th>
                                <th>Horas/Semana</th>
                                <th>Alumnos Inscriptos</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody id="materiasTableBody">
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

<!-- Modal Materia -->
<div id="modalAgregarAlumnos" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="content-title-modal">
                <div class="ico-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <g fill="none" stroke="#5046e5" stroke-linecap="round" stroke-linejoin="round"
                           stroke-width="1.5">
                            <path d="M15 8A5 5 0 1 0 5 8a5 5 0 0 0 10 0m2.5 13v-7M14 17.5h7"/>
                            <path d="M3 20a7 7 0 0 1 11-5.745"/>
                        </g>
                    </svg>
                </div>
                <h3 class="modal-title" id="modalMateriaTitle">Agregar Alumnos</h3>
            </div>
            <button class="modal-close" onclick="closeModal('modalAgregarAlumnos')">&times;</button>
        </div>
        <form id="formAgregarAlumnos">
            <div class="modal-body">
                <input type="hidden" id="materia_id" name="materia_id">

                <div class="form-group">
                    <label for="numero_alumnos" class="form-label">Numero de Alumnos</label>
                    <input type="number" id="numero_alumnos" name="numero_alumnos" class="form-input"
                           placeholder="Ej: 26" max="200" min="7" required>
                </div>

                <!-- Agregar un Sub-Titulo -->
                <h4 class="sub-title-modal" style="margin-bottom: 5px;">Datos para el Grupo</h4>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="max_alumnos" class="form-label">Máximo de Alumnos por Grupo</label>
                        <input type="number" id="max_alumnos" name="max_alumnos" class="form-input" max="30" min="7"
                               value="30" required>
                    </div>

                    <div class="form-group">
                        <label for="min_alumnos" class="form-label">Mínimo de Alumnos por Grupo</label>
                        <input type="number" id="min_alumnos" name="min_alumnos" class="form-input" max="10" min="1"
                               value="7" required>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Semestre</label>
                        <input type="number" id="semestre_actual" name="semestre_actual" class="form-input" min="1"
                               max="10" placeholder="Ej: 3" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="periodo_academico" class="form-label">Periodo Académico</label>
                        <input type="text" id="periodo_academico" name="periodo_academico" class="form-input"
                               placeholder="Ej: Agosto-Diciembre 2024" required>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAgregarAlumnos')">Cancelar
                </button>
                <button type="submit" class="btn btn-primary">Generar los Grupos</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal información -->
<div id="modal-info-generated" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="content-title-modal">
                <div class="ico-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 256 256"><path fill="#5046e5" d="M243.6 148.8a6 6 0 0 1-8.4-1.2A53.58 53.58 0 0 0 192 126a6 6 0 0 1 0-12a26 26 0 1 0-25.18-32.5a6 6 0 0 1-11.62-3a38 38 0 1 1 59.91 39.63a65.7 65.7 0 0 1 29.69 22.27a6 6 0 0 1-1.2 8.4M189.19 213a6 6 0 0 1-2.19 8.2a5.9 5.9 0 0 1-3 .81a6 6 0 0 1-5.2-3a59 59 0 0 0-101.62 0a6 6 0 1 1-10.38-6a70.1 70.1 0 0 1 36.2-30.46a46 46 0 1 1 50.1 0A70.1 70.1 0 0 1 189.19 213M128 178a34 34 0 1 0-34-34a34 34 0 0 0 34 34m-58-58a6 6 0 0 0-6-6a26 26 0 1 1 25.18-32.51a6 6 0 1 0 11.62-3a38 38 0 1 0-59.91 39.63A65.7 65.7 0 0 0 11.2 140.4a6 6 0 1 0 9.6 7.2A53.58 53.58 0 0 1 64 126a6 6 0 0 0 6-6" stroke-width="6.5" stroke="#5046e5"/></svg>
                </div>
                <h3 class="modal-title" id="modalMateriaTitle">Grupos Generados</h3>
            </div>
            <button class="modal-close" onclick="closeModal('modal-info-generated')">&times;</button>
        </div>

        <div class="modal-body">
            <div id="info-generated-content">
                <!-- Aquí se insertará el contenido dinámicamente -->
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-info-generated')">Cerrar</button>
            <a type="button" href="grupos.php" class="btn btn-primary">Ir a Grupos</a>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="../js/main.js"></script>
<script type="module" src="../js/alumnos.js"></script>
</body>
</html>
