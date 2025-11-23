<?php

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Manuelromero20@');
define('DB_NAME', 'railway');
define('DB_PORT', '3306');

/*
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Manuelromero20@');
define('DB_NAME', 'sistema_horarios');
define('DB_PORT', '3306');

define('DB_HOST', 'hopper.proxy.rlwy.net');
define('DB_USER', 'root');
define('DB_PASS', 'HOVuDjmacUhnMnByaKxNjfGRnQVowyON');
define('DB_NAME', 'railway');
define('DB_PORT', '45520');
*/

// Zona horaria
date_default_timezone_set('America/Mexico_City');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME,DB_PORT);
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

// Función para verificar si el usuario está autenticado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Función para redirigir si no está autenticado
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../index.php');
        exit();
    }
}

// Función para limpiar datos de entrada
function cleanInput($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Función para generar respuestas JSON
function jsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}
?>
