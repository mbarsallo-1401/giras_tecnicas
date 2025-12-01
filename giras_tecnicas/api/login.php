<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/BitacoraAcceso.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../utils/validators.php';

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Método no permitido', 405);
}

// Obtener datos JSON
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Si no hay JSON, intentar obtener datos de POST
if (!$data) {
    $data = $_POST;
}

// Validar campos requeridos
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!Validator::required($email) || !Validator::required($password)) {
    Response::error('El correo y la contraseña son obligatorios');
}

if (!Validator::email($email)) {
    Response::error('El correo electrónico no es válido');
}

// Sanitizar entrada
$email = Validator::sanitize($email);

// Buscar usuario en la base de datos
$usuarioModel = new Usuario();
$usuario = $usuarioModel->buscarPorCorreo($email);

if (!$usuario) {
    Response::error('Credenciales incorrectas');
}

// Verificar contraseña
$passwordValida = false;

if (password_verify($password, $usuario['ContrasenaHash'])) {
    $passwordValida = true;
} elseif ($password === $usuario['ContrasenaHash']) {
    // Para los datos de prueba que no están hasheados
    $passwordValida = true;
}

if (!$passwordValida) {
    Response::error('Credenciales incorrectas');
}

// Crear sesión
Session::set('usuario_id', $usuario['UsuarioID']);
Session::set('usuario_correo', $usuario['Correo']);
Session::set('usuario_nombre', $usuario['Nombre']);
Session::set('usuario_rol', $usuario['Rol']);

// Registrar acceso en bitácora
try {
    $bitacoraModel = new BitacoraAcceso();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
    $navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
    $bitacoraModel->registrar($usuario['UsuarioID'], $ip, $navegador);
} catch (Exception $e) {
    // No fallar el login si hay error en bitácora
    error_log('Error al registrar en bitácora: ' . $e->getMessage());
}

// Preparar respuesta
$response = [
    'usuario' => [
        'id' => $usuario['UsuarioID'],
        'nombre' => $usuario['Nombre'],
        'correo' => $usuario['Correo'],
        'rol' => $usuario['Rol']
    ]
];

Response::success('Inicio de sesión exitoso', $response);
?>