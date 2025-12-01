
<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../utils/validators.php';
require_once __DIR__ . '/../middleware/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$inscripcionModel = new Inscripcion();

// Requiere autenticación para todos los endpoints
AuthMiddleware::requireAuth();

// GET - Listar inscripciones
if ($method === 'GET') {
    // Por usuario
    if (isset($_GET['usuario_id'])) {
        $usuarioID = intval($_GET['usuario_id']);
        
        // Solo puede ver sus propias inscripciones o ser admin
        if ($usuarioID !== Session::getUserId() && Session::getUserRole() !== 'Administrador') {
            Response::forbidden();
        }
        
        $inscripciones = $inscripcionModel->listarPorUsuario($usuarioID);
        Response::success('Inscripciones obtenidas', $inscripciones);
    }
    
    // Por gira
    if (isset($_GET['gira_id'])) {
        $giraID = intval($_GET['gira_id']);
        $inscripciones = $inscripcionModel->listarPorGira($giraID);
        Response::success('Inscripciones obtenidas', $inscripciones);
    }
    
    Response::error('Debe especificar usuario_id o gira_id');
}

// POST - Crear inscripción
if ($method === 'POST') {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    $usuarioID = Session::getUserId(); // Usuario actual
    $giraID = $data['gira_id'] ?? 0;
    $estado = 'Registrado';
    
    if (!$giraID) {
        Response::error('Debe especificar la gira');
    }
    
    $resultado = $inscripcionModel->insertar($usuarioID, $giraID, $estado);
    
    if ($resultado === false) {
        Response::error('Ya estás inscrito en esta gira o ocurrió un error');
    }
    
    Response::success('Inscripción realizada exitosamente', ['id' => $resultado], 201);
}

// PUT - Actualizar estado de inscripción
if ($method === 'PUT') {
    AuthMiddleware::requireAnyRole(['Organizador', 'Administrador']);
    
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    $inscripcionID = $data['id'] ?? 0;
    $nuevoEstado = $data['estado'] ?? '';
    
    if (!$inscripcionID || !$nuevoEstado) {
        Response::error('Debe especificar ID y estado');
    }
    
    // Sanitizar
    $nuevoEstado = Validator::sanitize($nuevoEstado);
    
    $resultado = $inscripcionModel->actualizarEstado($inscripcionID, $nuevoEstado);
    
    if (!$resultado) {
        Response::error('Error al actualizar el estado');
    }
    
    Response::success('Estado actualizado exitosamente');
}

// DELETE - Cancelar inscripción
if ($method === 'DELETE') {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    if (!$data) {
        parse_str(file_get_contents('php://input'), $data);
    }
    
    $inscripcionID = $data['id'] ?? $_GET['id'] ?? 0;
    
    if (!$inscripcionID) {
        Response::error('Debe especificar el ID de la inscripción');
    }
    
    $resultado = $inscripcionModel->eliminar($inscripcionID);
    
    if (!$resultado) {
        Response::error('Error al cancelar la inscripción');
    }
    
    Response::success('Inscripción cancelada exitosamente');
}
?>
