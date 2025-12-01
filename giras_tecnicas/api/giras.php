
<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Gira.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../utils/validators.php';
require_once __DIR__ . '/../middleware/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$giraModel = new Gira();
$inscripcionModel = new Inscripcion();

// GET - Listar giras
if ($method === 'GET') {
    // Si se pide una gira específica por ID
    if (isset($_GET['id'])) {
        $giraId = intval($_GET['id']);
        $gira = $giraModel->buscarPorID($giraId);
        
        if (!$gira) {
            Response::notFound('Gira no encontrada');
        }
        
        // Contar inscritos
        $totalInscritos = $inscripcionModel->contarPorGira($giraId);
        $gira['totalInscritos'] = $totalInscritos;
        
        Response::success('Gira encontrada', $gira);
    }
    
    // Si se pide solo giras disponibles (futuras)
    if (isset($_GET['disponibles']) && $_GET['disponibles'] === 'true') {
        $giras = $giraModel->listarDisponibles();
    } else {
        $giras = $giraModel->listar();
    }
    
    // Agregar total de inscritos a cada gira
    foreach ($giras as &$gira) {
        $gira['totalInscritos'] = $inscripcionModel->contarPorGira($gira['GiraID']);
    }
    
    Response::success('Giras obtenidas exitosamente', $giras);
}

// POST - Crear nueva gira (solo Organizadores y Administradores)
if ($method === 'POST') {
    AuthMiddleware::requireAnyRole(['Organizador', 'Administrador']);
    
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    $nombre = $data['nombre'] ?? '';
    $fecha = $data['fecha'] ?? '';
    $lugar = $data['lugar'] ?? '';
    $descripcion = $data['descripcion'] ?? '';
    $organizadorID = $data['organizadorID'] ?? 1; // Default a 1 si no se especifica
    
    // Validaciones
    if (!Validator::required($nombre)) {
        Response::error('El nombre de la gira es obligatorio');
    }
    
    if (!Validator::required($fecha)) {
        Response::error('La fecha es obligatoria');
    }
    
    if (!Validator::required($lugar)) {
        Response::error('El lugar es obligatorio');
    }
    
    // Sanitizar
    $nombre = Validator::sanitize($nombre);
    $lugar = Validator::sanitize($lugar);
    $descripcion = Validator::sanitize($descripcion);
    
    // Insertar
    $resultado = $giraModel->insertar($nombre, $fecha, $lugar, $descripcion, $organizadorID);
    
    if (!$resultado) {
        Response::error('Error al crear la gira');
    }
    
    Response::success('Gira creada exitosamente', ['id' => $resultado], 201);
}

// PUT - Actualizar gira (solo Organizadores y Administradores)
if ($method === 'PUT') {
    AuthMiddleware::requireAnyRole(['Organizador', 'Administrador']);
    
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    $giraID = $data['id'] ?? 0;
    $nombre = $data['nombre'] ?? '';
    $fecha = $data['fecha'] ?? '';
    $lugar = $data['lugar'] ?? '';
    $descripcion = $data['descripcion'] ?? '';
    $organizadorID = $data['organizadorID'] ?? 1;
    
    if (!$giraID) {
        Response::error('ID de gira no especificado');
    }
    
    // Sanitizar
    $nombre = Validator::sanitize($nombre);
    $lugar = Validator::sanitize($lugar);
    $descripcion = Validator::sanitize($descripcion);
    
    $resultado = $giraModel->actualizar($giraID, $nombre, $fecha, $lugar, $descripcion, $organizadorID);
    
    if (!$resultado) {
        Response::error('Error al actualizar la gira');
    }
    
    Response::success('Gira actualizada exitosamente');
}

// DELETE - Eliminar gira (solo Administradores)
if ($method === 'DELETE') {
    AuthMiddleware::requireRole('Administrador');
    
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    if (!$data) {
        parse_str(file_get_contents('php://input'), $data);
    }
    
    $giraID = $data['id'] ?? $_GET['id'] ?? 0;
    
    if (!$giraID) {
        Response::error('ID de gira no especificado');
    }
    
    $resultado = $giraModel->eliminar($giraID);
    
    if (!$resultado) {
        Response::error('Error al eliminar la gira');
    }
    
    Response::success('Gira eliminada exitosamente');
}
?>
