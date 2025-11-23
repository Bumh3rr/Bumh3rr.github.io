<?php
require_once 'config.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listGrupos();
        break;
    case 'get':
        getGrupo();
        break;
    case 'create':
    case 'update':
    case 'delete':
        if ($action === 'create') createGrupo();
        if ($action === 'update') updateGrupo();
        if ($action === 'delete') deleteGrupo();
        break;
    default:
        jsonResponse(false, 'Acción no válida');
}

function listGrupos() {
    global $conn;

    $sql = "SELECT g.*, 
            m.nombre as materia_nombre,
            COALESCE(CONCAT_WS(' ', u.nombre, u.apellido), '') as profesor_nombre,
            g.alumnos_inscriptos as num_estudiantes
            FROM grupos g
            JOIN materias m ON g.materia_id = m.id
            LEFT JOIN docente u ON g.profesor_id = u.id
            WHERE g.activo = 1
            ORDER BY g.periodo_academico DESC, m.nombre";

    $result = $conn->query($sql);
    if (!$result) {
        jsonResponse(false, 'Error en la consulta de grupos');
    }
    $grupos = [];

    while ($row = $result->fetch_assoc()) {
        $grupos[] = $row;
    }

    jsonResponse(true, 'Grupos obtenidos exitosamente', $grupos);
}

function getGrupo() {
    global $conn;
    
    $id = cleanInput($_GET['id'] ?? '');
    
    if (empty($id)) {
        jsonResponse(false, 'ID no proporcionado');
    }
    
    $sql = "SELECT * FROM grupos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        jsonResponse(false, 'Grupo no encontrado');
    }
    
    $grupo = $result->fetch_assoc();
    jsonResponse(true, 'Grupo obtenido exitosamente', $grupo);
}

function createGrupo() {
    global $conn;
    
    $materia_id = cleanInput($_POST['materia_id'] ?? '');
    $profesor_id = cleanInput($_POST['profesor_id'] ?? '');
    $nombre = cleanInput($_POST['nombre'] ?? '');
    $cupo_maximo = cleanInput($_POST['cupo_maximo'] ?? '');
    $semestre_actual = cleanInput($_POST['semestre_actual'] ?? '');
    $periodo_academico = cleanInput($_POST['periodo_academico'] ?? '');
    
    if (empty($materia_id) || empty($profesor_id) || empty($nombre) || empty($cupo_maximo)) {
        jsonResponse(false, 'Todos los campos obligatorios deben ser completados');
    }
    
    $sql = "INSERT INTO grupos (materia_id, profesor_id, nombre, cupo_maximo, semestre_actual, periodo_academico) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisiss", $materia_id, $profesor_id, $nombre, $cupo_maximo, $semestre_actual, $periodo_academico);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Grupo creado exitosamente');
    } else {
        jsonResponse(false, 'Error al crear el grupo');
    }
}

function updateGrupo() {
    global $conn;
    
    $id = cleanInput($_POST['grupo_id'] ?? '');
    $materia_id = cleanInput($_POST['materia_id'] ?? '');
    $profesor_id = cleanInput($_POST['profesor_id'] ?? '');
    $nombre = cleanInput($_POST['nombre'] ?? '');
    $cupo_maximo = cleanInput($_POST['cupo_maximo'] ?? '');
    $semestre_actual = cleanInput($_POST['semestre_actual'] ?? '');
    $periodo_academico = cleanInput($_POST['periodo_academico'] ?? '');
    
    if (empty($id) || empty($materia_id) || empty($profesor_id) || empty($nombre) || empty($cupo_maximo)) {
        jsonResponse(false, 'Todos los campos obligatorios deben ser completados');
    }
    
    $sql = "UPDATE grupos SET materia_id = ?, profesor_id = ?, nombre = ?, cupo_maximo = ?, semestre_actual = ?, periodo_academico = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisissi", $materia_id, $profesor_id, $nombre, $cupo_maximo, $semestre_actual, $periodo_academico, $id);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Grupo actualizado exitosamente');
    } else {
        jsonResponse(false, 'Error al actualizar el grupo');
    }
}

function deleteGrupo() {
    global $conn;
    
    $id = cleanInput($_POST['id'] ?? '');
    
    if (empty($id)) {
        jsonResponse(false, 'ID no proporcionado');
    }
    
    $sql = "DELETE FROM grupos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Grupo eliminado exitosamente');
    } else {
        jsonResponse(false, 'Error al eliminar el grupo');
    }
}
?>
