<?php
require_once 'config.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listCarreras();
        break;
    case 'get':
        getCarrera();
        break;
    case 'create':
    case 'update':
    case 'delete':
        if ($action === 'create') createCarrera();
        if ($action === 'update') updateCarrera();
        if ($action === 'delete') deleteCarrera();
        break;
    default:
        jsonResponse(false, 'Acción no válida');
}

function listCarreras() {
    global $conn;
    
    $sql = "SELECT * FROM carreras WHERE activo = 1 ORDER BY nombre";
    $result = $conn->query($sql);
    $carreras = [];
    
    while ($row = $result->fetch_assoc()) {
        $carreras[] = $row;
    }
    
    jsonResponse(true, 'Carreras obtenidas exitosamente', $carreras);
}

function getCarrera() {
    global $conn;
    
    $id = cleanInput($_GET['id'] ?? '');
    
    if (empty($id)) {
        jsonResponse(false, 'ID no proporcionado');
    }
    
    $sql = "SELECT * FROM carreras WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        jsonResponse(false, 'Carrera no encontrada');
    }
    
    $carrera = $result->fetch_assoc();
    jsonResponse(true, 'Carrera obtenida exitosamente', $carrera);
}

function createCarrera() {
    global $conn;
    
    $nombre = cleanInput($_POST['nombre'] ?? '');
    $codigo = cleanInput($_POST['codigo'] ?? '');
    $descripcion = cleanInput($_POST['descripcion'] ?? '');
    $duracion_semestres = cleanInput($_POST['duracion_semestres'] ?? '');
    
    if (empty($nombre) || empty($codigo) || empty($duracion_semestres)) {
        jsonResponse(false, 'Todos los campos obligatorios deben ser completados');
    }
    
    // Verificar si el código ya existe
    $sql = "SELECT id FROM carreras WHERE codigo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        jsonResponse(false, 'El código ya está registrado');
    }
    
    $sql = "INSERT INTO carreras (nombre, codigo, descripcion, duracion_semestres) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre, $codigo, $descripcion, $duracion_semestres);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Carrera creada exitosamente');
    } else {
        jsonResponse(false, 'Error al crear la carrera');
    }
}

function updateCarrera() {
    global $conn;
    
    $id = cleanInput($_POST['carrera_id'] ?? '');
    $nombre = cleanInput($_POST['nombre'] ?? '');
    $codigo = cleanInput($_POST['codigo'] ?? '');
    $descripcion = cleanInput($_POST['descripcion'] ?? '');
    $duracion_semestres = cleanInput($_POST['duracion_semestres'] ?? '');
    
    if (empty($id) || empty($nombre) || empty($codigo) || empty($duracion_semestres)) {
        jsonResponse(false, 'Todos los campos obligatorios deben ser completados');
    }
    
    $sql = "UPDATE carreras SET nombre = ?, codigo = ?, descripcion = ?, duracion_semestres = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $nombre, $codigo, $descripcion, $duracion_semestres, $id);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Carrera actualizada exitosamente');
    } else {
        jsonResponse(false, 'Error al actualizar la carrera');
    }
}

function deleteCarrera() {
    global $conn;
    
    $id = cleanInput($_POST['id'] ?? '');
    
    if (empty($id)) {
        jsonResponse(false, 'ID no proporcionado');
    }
    
    $sql = "DELETE FROM carreras WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Carrera eliminada exitosamente');
    } else {
        jsonResponse(false, 'Error al eliminar la carrera');
    }
}
?>
