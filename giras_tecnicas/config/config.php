
<?php
// Configuraciones globales de la aplicación
define('BASE_PATH', '/home/ubuntu/code_artifacts/giras_tecnicas');
define('BASE_URL', '/');

// Zona horaria
date_default_timezone_set('America/Panama');

// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers CORS (si es necesario)
header('Content-Type: application/json; charset=utf-8');

// Permitir métodos HTTP
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400');
    exit(0);
}
?>
