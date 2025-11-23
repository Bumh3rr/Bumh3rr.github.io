<?php
require_once 'config.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listHorarios();
        break;
    case 'schedule':
        getSchedule();
        break;
    case 'my_schedule':
        getMySchedule();
        break;
    case 'create':
        createHorario();
        break;
    case 'delete':
        deleteHorario();
        break;
    default:
        jsonResponse(false, 'Acción no válida');
}

function listHorarios() {
    global $conn;
    
    $grupo = cleanInput($_GET['grupo'] ?? '');
    
$sql = "SELECT h.*, 
        g.nombre as grupo_nombre,
        m.nombre as materia_nombre,
        CONCAT(u.nombre, ' ', u.apellido) as profesor_nombre,
        a.nombre as aula_nombre,
        a.edificio as aula_edificio
        FROM horarios h
        JOIN grupos g ON h.grupo_id = g.id
        JOIN materias m ON g.materia_id = m.id
        JOIN docente u ON g.profesor_id = u.id
        JOIN aulas a ON h.aula_id = a.id
        WHERE 1=1";
    
    if ($grupo) {
        $sql .= " AND h.grupo_id = '$grupo'";
    }
    
    $sql .= " ORDER BY 
              FIELD(h.dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'),
              h.hora_inicio";
    
    $result = $conn->query($sql);
    $horarios = [];
    
    while ($row = $result->fetch_assoc()) {
        $horarios[] = $row;
    }
    
    jsonResponse(true, 'Horarios obtenidos exitosamente', $horarios);
}

function getSchedule() {
    global $conn;
    
    $grupo = cleanInput($_GET['grupo'] ?? '');
    
    $sql = "SELECT h.*,
            g.nombre as grupo_nombre,
            m.nombre as materia_nombre,
            CONCAT(u.nombre, ' ', u.apellido) as profesor_nombre,
            a.nombre as aula_nombre,
            a.edificio as aula_edificio
            FROM horarios h
            JOIN grupos g ON h.grupo_id = g.id
            JOIN materias m ON g.materia_id = m.id
            LEFT JOIN docente u ON g.profesor_id = u.id
            JOIN aulas a ON h.aula_id = a.id
            WHERE 1=1";
    
    if ($grupo) {
        $sql .= " AND h.grupo_id = '$grupo'";
    }
    
    $result = $conn->query($sql);
    $horarios = [];
    
    while ($row = $result->fetch_assoc()) {
        $horarios[] = $row;
    }
    
    jsonResponse(true, 'Horario obtenido exitosamente', $horarios);
}

function getMySchedule() {
    global $conn;
    
    $user_id = $_SESSION['user_id'];
    $rol = $_SESSION['rol'];
    
    if ($rol === 'profesor') {
        // Obtener horarios de los grupos del profesor
        $sql = "SELECT h.*, 
                g.nombre as grupo_nombre,
                m.nombre as materia_nombre,
                a.nombre as aula_nombre,
                a.edificio as aula_edificio
                FROM horarios h
                JOIN grupos g ON h.grupo_id = g.id
                JOIN materias m ON g.materia_id = m.id
                JOIN aulas a ON h.aula_id = a.id
                WHERE g.profesor_id = ?
                ORDER BY 
                  FIELD(h.dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'),
                  h.hora_inicio";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
    } elseif ($rol === 'estudiante') {
        // Obtener horarios de los grupos en los que está inscrito el estudiante
        $sql = "SELECT h.*, 
                g.nombre as grupo_nombre,
                m.nombre as materia_nombre,
                CONCAT(u.nombre, ' ', u.apellido) as profesor_nombre,
                a.nombre as aula_nombre,
                a.edificio as aula_edificio
                FROM horarios h
                JOIN grupos g ON h.grupo_id = g.id
                JOIN materias m ON g.materia_id = m.id
                JOIN usuarios u ON g.profesor_id = u.id
                JOIN aulas a ON h.aula_id = a.id
                JOIN inscripciones i ON g.id = i.grupo_id
                WHERE i.estudiante_id = ?
                ORDER BY 
                  FIELD(h.dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'),
                  h.hora_inicio";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
    } else {
        jsonResponse(false, 'No tienes permisos para ver horarios');
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $horarios = [];
    
    while ($row = $result->fetch_assoc()) {
        $horarios[] = $row;
    }
    
    jsonResponse(true, 'Horario obtenido exitosamente', $horarios);
}

function createHorario() {
    global $conn;
    
    $grupo_id = cleanInput($_POST['grupo_id'] ?? '');
    $aula_id = cleanInput($_POST['aula_id'] ?? '');
    $dia_semana = cleanInput($_POST['dia_semana'] ?? '');
    $hora_inicio = cleanInput($_POST['hora_inicio'] ?? '');
    $hora_fin = cleanInput($_POST['hora_fin'] ?? '');
    
    // Validaciones
    if (empty($grupo_id) || empty($aula_id) || empty($dia_semana) || empty($hora_inicio) || empty($hora_fin)) {
        jsonResponse(false, 'Todos los campos son obligatorios');
    }
    
    if ($hora_inicio >= $hora_fin) {
        jsonResponse(false, 'La hora de fin debe ser mayor que la hora de inicio');
    }
    
    // Verificar conflicto de aula
    $sql = "SELECT COUNT(*) as conflictos 
            FROM horarios 
            WHERE aula_id = ? 
            AND dia_semana = ? 
            AND (
                (hora_inicio < ? AND hora_fin > ?) OR
                (hora_inicio < ? AND hora_fin > ?) OR
                (hora_inicio >= ? AND hora_fin <= ?)
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $aula_id, $dia_semana, $hora_fin, $hora_inicio, $hora_fin, $hora_fin, $hora_inicio, $hora_fin);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['conflictos'] > 0) {
        jsonResponse(false, 'El aula ya está ocupada en ese horario');
    }
    
    // Verificar conflicto de profesor
    $sql = "SELECT COUNT(*) as conflictos 
            FROM horarios h
            JOIN grupos g ON h.grupo_id = g.id
            WHERE g.profesor_id = (SELECT profesor_id FROM grupos WHERE id = ?)
            AND h.dia_semana = ? 
            AND (
                (h.hora_inicio < ? AND h.hora_fin > ?) OR
                (h.hora_inicio < ? AND h.hora_fin > ?) OR
                (h.hora_inicio >= ? AND h.hora_fin <= ?)
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $grupo_id, $dia_semana, $hora_fin, $hora_inicio, $hora_fin, $hora_fin, $hora_inicio, $hora_fin);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['conflictos'] > 0) {
        jsonResponse(false, 'El profesor ya tiene una clase asignada en ese horario');
    }
    
    // Insertar horario
    $sql = "INSERT INTO horarios (grupo_id, aula_id, dia_semana, hora_inicio, hora_fin) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $grupo_id, $aula_id, $dia_semana, $hora_inicio, $hora_fin);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Horario asignado exitosamente');
    } else {
        jsonResponse(false, 'Error al asignar el horario');
    }
}

function deleteHorario() {
    global $conn;
    
    $id = cleanInput($_POST['id'] ?? '');
    
    if (empty($id)) {
        jsonResponse(false, 'ID no proporcionado');
    }
    
    $sql = "DELETE FROM horarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Horario eliminado exitosamente');
    } else {
        jsonResponse(false, 'Error al eliminar el horario');
    }
}
?>
