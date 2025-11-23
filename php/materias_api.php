<?php
require_once 'config.php';
requireLogin();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listMaterias();
        break;
    case 'get':
        getMateria();
        break;
    case 'create':
    case 'update':
    case 'delete':
        if ($action === 'create') createMateria();
        if ($action === 'update') updateMateria();
        if ($action === 'delete') deleteMateria();
        break;
    default:
        jsonResponse(false, 'Acción no válida');
}

// Reemplazar la función listMaterias en `php/materias_api.php`
function listMaterias() {
    global $conn;

    $search = cleanInput($_GET['search'] ?? '');
    $carrera = cleanInput($_GET['carrera'] ?? '');
    $semestre = cleanInput($_GET['semestre'] ?? '');

    $where = "m.activo = 1";
    $params = [];
    $types = '';

    if ($search !== '') {
        $where .= " AND (m.nombre LIKE ? OR m.codigo LIKE ?)";
        $like = "%{$search}%";
        $params[] = $like;
        $params[] = $like;
        $types .= 'ss';
    }

    if ($carrera !== '') {
        // asegurar entero
        $where .= " AND m.carrera_id = ?";
        $params[] = (int)$carrera;
        $types .= 'i';
    }

    if ($semestre !== '') {
        $where .= " AND m.semestre = ?";
        $params[] = (int)$semestre;
        $types .= 'i';
    }

    $sql = "SELECT m.*, c.nombre as carrera_nombre 
            FROM materias m
            JOIN carreras c ON m.carrera_id = c.id
            WHERE $where
            ORDER BY c.nombre, m.semestre, m.nombre";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        jsonResponse(false, 'Error en la consulta: ' . $conn->error);
    }

    if (!empty($params)) {
        // bind_param requiere referencias
        $bind_names = [];
        $bind_names[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_names[] = &$params[$i];
        }
        call_user_func_array([$stmt, 'bind_param'], $bind_names);
    }

    if (!$stmt->execute()) {
        jsonResponse(false, 'Error al ejecutar la consulta');
    }

    $result = $stmt->get_result();
    $materias = [];

    while ($row = $result->fetch_assoc()) {
        $materias[] = $row;
    }

    jsonResponse(true, 'Materias obtenidas exitosamente', $materias);
}

function getMateria() {
    global $conn;
    
    $id = cleanInput($_GET['id'] ?? '');
    
    if (empty($id)) {
        jsonResponse(false, 'ID no proporcionado');
    }
    
    $sql = "SELECT * FROM materias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        jsonResponse(false, 'Materia no encontrada');
    }
    
    $materia = $result->fetch_assoc();
    jsonResponse(true, 'Materia obtenida exitosamente', $materia);
}

function createMateria() {
    global $conn;
    
    $nombre = cleanInput($_POST['nombre'] ?? '');
    $codigo = cleanInput($_POST['codigo'] ?? '');
    $carrera_id = cleanInput($_POST['carrera_id'] ?? '');
    $semestre = cleanInput($_POST['semestre'] ?? '');
    $creditos = cleanInput($_POST['creditos'] ?? '');
    $horas_semanales = cleanInput($_POST['horas_semanales'] ?? '');
    $descripcion = cleanInput($_POST['descripcion'] ?? '');
    
    if (empty($nombre) || empty($codigo) || empty($carrera_id) || empty($semestre) || empty($creditos) || empty($horas_semanales)) {
        jsonResponse(false, 'Todos los campos obligatorios deben ser completados');
    }
    
    // Verificar si el código ya existe
    $sql = "SELECT id FROM materias WHERE codigo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        jsonResponse(false, 'El código ya está registrado');
    }
    
    $sql = "INSERT INTO materias (nombre, codigo, carrera_id, semestre, creditos, horas_semanales, descripcion) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiiis", $nombre, $codigo, $carrera_id, $semestre, $creditos, $horas_semanales, $descripcion);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Materia creada exitosamente');
    } else {
        jsonResponse(false, 'Error al crear la materia');
    }
}

function updateMateria() {
    global $conn;
    
    $id = cleanInput($_POST['materia_id'] ?? '');
    $nombre = cleanInput($_POST['nombre'] ?? '');
    $codigo = cleanInput($_POST['codigo'] ?? '');
    $carrera_id = cleanInput($_POST['carrera_id'] ?? '');
    $semestre = cleanInput($_POST['semestre'] ?? '');
    $creditos = cleanInput($_POST['creditos'] ?? '');
    $horas_semanales = cleanInput($_POST['horas_semanales'] ?? '');
    $descripcion = cleanInput($_POST['descripcion'] ?? '');
    
    if (empty($id) || empty($nombre) || empty($codigo) || empty($carrera_id) || empty($semestre) || empty($creditos) || empty($horas_semanales)) {
        jsonResponse(false, 'Todos los campos obligatorios deben ser completados');
    }
    
    $sql = "UPDATE materias SET nombre = ?, codigo = ?, carrera_id = ?, semestre = ?, creditos = ?, horas_semanales = ?, descripcion = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiiisi", $nombre, $codigo, $carrera_id, $semestre, $creditos, $horas_semanales, $descripcion, $id);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Materia actualizada exitosamente');
    } else {
        jsonResponse(false, 'Error al actualizar la materia');
    }
}

function deleteMateria() {
    global $conn;
    
    $id = cleanInput($_POST['id'] ?? '');
    
    if (empty($id)) {
        jsonResponse(false, 'ID no proporcionado');
    }
    
    $sql = "DELETE FROM materias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Materia eliminada exitosamente');
    } else {
        jsonResponse(false, 'Error al eliminar la materia');
    }
}
?>
