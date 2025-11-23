<?php
require_once 'config.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create':
        createMateria();
        break;
    default:
        jsonResponse(false, 'Acción no válida');
}

function createMateria()
{
    global $conn;

    $materia_id = cleanInput($_POST['materia_id'] ?? '');
    $numero_alumnos = cleanInput($_POST['numero_alumnos'] ?? '');
    $max_alumnos = cleanInput($_POST['max_alumnos'] ?? '');
    $min_alumnos = cleanInput($_POST['min_alumnos'] ?? 7);
    $periodo_academico = cleanInput($_POST['periodo_academico'] ?? '');
    $semestre_actual = cleanInput($_POST['semestre_actual'] ?? '');

    if (empty($materia_id) || empty($numero_alumnos) || empty($max_alumnos) || empty($periodo_academico) || empty($semestre_actual)) {
        jsonResponse(false, 'Todos los campos obligatorios deben ser completados');
    }

    // Obtener la informacion de la materia
    $sql = "SELECT * FROM materias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $materia_id);
    $stmt->execute();
    $result_materia = $stmt->get_result();
    if ($result_materia->num_rows === 0) {
        jsonResponse(false, 'Materia no encontrada');
    }

    $iniciales_grupo = strtoupper(substr($result_materia->fetch_assoc()['nombre'], 0, 3));
    $iniciales_grupo .= '-';

    $map_grupo = [
        1 => 'A',
        2 => 'B',
        3 => 'C',
        4 => 'D',
        5 => 'E',
        6 => 'F',
        7 => 'G',
        8 => 'H',
        9 => 'I',
        10 => 'J',
    ];

    $numero_grupos_a_crear = ceil($numero_alumnos / $max_alumnos);
    $data = [];
    $grupo_omitido =[];

    for ($i = 1; $i <= $numero_grupos_a_crear; $i++) {

        $nombre_grupo = $iniciales_grupo . $map_grupo[$i];
        $alumnos_inscriptos = ($i < $numero_grupos_a_crear) ? $max_alumnos : ($numero_alumnos - ($max_alumnos * ($i - 1)));
        if ($alumnos_inscriptos < $min_alumnos) {
            $grupo_omitido[] = [
                'nombre_grupo' => $nombre_grupo,
                'alumnos_inscriptos' => $alumnos_inscriptos,
                'motivo' => 'No cumple con el mínimo de alumnos requerido'
            ];
            continue;
        }

        $sql = "INSERT INTO grupos (materia_id, nombre, cupo_maximo, alumnos_inscriptos, semestre_actual, periodo_academico) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isiiss", $materia_id, $nombre_grupo, $max_alumnos, $alumnos_inscriptos, $semestre_actual, $periodo_academico);

        if (!$stmt->execute()) {
            jsonResponse(false, 'Error al crear el grupo ' . $nombre_grupo);
        }

        $data [] = [
            'grupo_id' => $stmt->insert_id,
            'nombre_grupo' => $nombre_grupo,
            'alumnos_inscriptos' => $alumnos_inscriptos
        ];
    }
    // Actualizar alumnos_inscriptos en la tabla materias
    $sql_update = "UPDATE materias SET alumnos_inscriptos =  ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $var1 = true;
    $stmt_update->bind_param("si", $var1, $materia_id);
    $stmt_update->execute();

    $data_summary = [
        'grupos_creados' => $data,
        'grupos_omitidos' => $grupo_omitido
    ];

    jsonResponse(true, 'Grupos creados exitosamente', $data_summary);
}
?>
