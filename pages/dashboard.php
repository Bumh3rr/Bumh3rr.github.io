<?php
require_once '../php/config.php';

// Obtener estadísticas
$stats = [
    'profesores' => $conn->query("SELECT COUNT(*) as total FROM docente WHERE activo = 1")->fetch_assoc()['total'],
    'materias' => $conn->query("SELECT COUNT(*) as total FROM materias WHERE activo = 1")->fetch_assoc()['total'],
    'grupos' => $conn->query("SELECT COUNT(*) as total FROM grupos WHERE activo = 1")->fetch_assoc()['total'],
    'aulas' => $conn->query("SELECT COUNT(*) as total FROM aulas WHERE activo = 1")->fetch_assoc()['total']
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <?php include './components/sidebar.php'; ?>

        <!-- Contenido Principal -->
        <div class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <h1 class="topbar-title">Dashboard</h1>
            </div>

            <!-- Contenido -->
            <div class="content-wrapper">
                <!-- Estadísticas -->
                <div class="stats-grid">

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon success">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $stats['profesores']; ?></div>
                        <div class="stat-label">Profesores</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon error">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $stats['materias']; ?></div>
                        <div class="stat-label">Materias</div>
                    </div>
                </div>

                <!-- Grupos Activos -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Grupos Activos</h2>
                        <a href="grupos.php" class="btn btn-primary btn-sm">Ver Todos</a>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Grupo</th>
                                        <th>Materia</th>
                                        <th>Profesor</th>
                                        <th>Estudiantes</th>
                                        <th>Periodo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT g.*, m.nombre as materia_nombre, 
                                            CONCAT(u.nombre, ' ', u.apellido) as profesor_nombre,
                                            (SELECT COUNT(*) FROM inscripciones WHERE grupo_id = g.id) as num_estudiantes
                                            FROM grupos g
                                            JOIN materias m ON g.materia_id = m.id
                                            JOIN usuarios u ON g.profesor_id = u.id
                                            WHERE g.activo = 1
                                            LIMIT 5";
                                    $result = $conn->query($sql);
                                    
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td><strong>{$row['nombre']}</strong></td>";
                                            echo "<td>{$row['materia_nombre']}</td>";
                                            echo "<td>{$row['profesor_nombre']}</td>";
                                            echo "<td>{$row['num_estudiantes']} / {$row['cupo_maximo']}</td>";
                                            echo "<td>{$row['periodo_academico']}</td>";
                                            echo "<td><span class='badge badge-success'>Activo</span></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>No hay grupos registrados</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/main.js"></script>
</body>
</html>
