
<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Usuario.php';
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
$nombre = $data['nombre'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$confirmPassword = $data['confirmPassword'] ?? '';

// Validaciones
if (!Validator::required($nombre)) {
    Response::error('El nombre es obligatorio');
}

if (!Validator::required($email) || !Validator::email($email)) {
    Response::error('El correo electrónico no es válido');
}

if (!Validator::required($password)) {
    Response::error('La contraseña es obligatoria');
}

if (!Validator::minLength($password, 6)) {
    Response::error('La contraseña debe tener al menos 6 caracteres');
}

if ($password !== $confirmPassword) {
    Response::error('Las contraseñas no coinciden');
}

// Sanitizar entrada
$nombre = Validator::sanitize($nombre);
$email = Validator::sanitize($email);

// Verificar si el usuario ya existe
$usuarioModel = new Usuario();
$usuarioExistente = $usuarioModel->buscarPorCorreo($email);

if ($usuarioExistente) {
    Response::error('Ya existe un usuario registrado con este correo');
}

// Hash de la contraseña
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// Por defecto, los nuevos usuarios son estudiantes
$rol = 'Estudiante';

// Insertar usuario
$resultado = $usuarioModel->insertar($nombre, $email, $passwordHash, $rol);

if (!$resultado) {
    Response::error('Error al registrar el usuario. Intente nuevamente');
}

// Iniciar sesión automáticamente
Session::set('usuario_id', $resultado);
Session::set('usuario_correo', $email);
Session::set('usuario_nombre', $nombre);
Session::set('usuario_rol', $rol);

// Preparar respuesta
$response = [
    'usuario' => [
        'id' => $resultado,
        'nombre' => $nombre,
        'correo' => $email,
        'rol' => $rol
    ]
];

Response::success('Registro exitoso', $response, 201);
?>
