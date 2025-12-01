<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'girastecnicas');

function conectarDB() {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conexion->connect_error) {
        die("❌ Error: " . $conexion->connect_error);
    }
    
    $conexion->set_charset("utf8mb4");
    return $conexion;
}

function cerrarDB($conexion) {
    if ($conexion) {
        $conexion->close();
    }
}
?>