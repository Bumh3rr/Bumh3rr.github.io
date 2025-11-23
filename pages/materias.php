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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link rel="stylesheet" href="../css/styles-modal.css">
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
            <h1 class="topbar-title">Gestión de Materias</h1>
            <div class="topbar-actions">
                <button class="btn btn-primary" onclick="openModal('modalMateria')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Nueva Materia
                </button>
            </div>
        </div>

        <div class="content-wrapper">
            <div id="alertContainer"></div>

            <!-- Filtros -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="grid grid-3">
                        <div class="form-group" style="margin-bottom: 0;">
                            <input type="text" id="searchInput" class="form-input" placeholder="Buscar por nombre o código...">
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
<div id="modalMateria" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="content-title-modal">
                <div class="ico-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="#5046e5" stroke-linecap="round" stroke-width="1.5" d="M21 16c0 2.828 0 4.243-.879 5.121C19.243 22 17.828 22 15 22H9c-2.828 0-4.243 0-5.121-.879C3 20.243 3 18.828 3 16V8c0-2.828 0-4.243.879-5.121C4.757 2 6.172 2 9 2h6c2.828 0 4.243 0 5.121.879C21 3.757 21 5.172 21 8v4M8 2v4m0 16V10m-6 2h2m-2 4h2M2 8h2m7.5-1.5h5m-5 3.5h5"/></svg>
                </div>
                <h3 class="modal-title" id="modalMateriaTitle">Nueva Materia</h3>
            </div>
            <button class="modal-close" onclick="closeModal('modalMateria')">&times;</button>
        </div>
        <form id="formMateria">
            <div class="modal-body">
                <input type="hidden" id="materia_id" name="materia_id">

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Código</label>
                        <input type="text" id="codigo" name="codigo" class="form-input" placeholder="Ej: POO-101" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Carrera</label>
                        <select id="carrera_id" name="carrera_id" class="form-input" required>
                            <option value="">Seleccionar...</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nombre de la Materia</label>
                    <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Ej: Programación Orientada a Objetos" required>
                </div>

                <div class="grid grid-3">
                    <div class="form-group">
                        <label class="form-label">Semestre</label>
                        <input type="number" id="semestre" name="semestre" class="form-input" min="1" max="12" placeholder="Ej: 3" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Créditos</label>
                        <input type="number" id="creditos" name="creditos" class="form-input" min="1" max="20" placeholder="Ej: 8" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Horas/Semana</label>
                        <input type="number" id="horas_semanales" name="horas_semanales" class="form-input" min="1" max="40" placeholder="Ej: 6" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-input" rows="3" placeholder="Descripción de la materia"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalMateria')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="../js/main.js"></script>
<script type="module" src="../js/materias.js"></script>
</body>
</html>