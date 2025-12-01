
<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../utils/response.php';

// Verificar si hay sesión activa
if (Session::isAuthenticated()) {
    Response::success('Sesión activa', [
        'usuario' => [
            'id' => Session::getUserId(),
            'nombre' => Session::getUserName(),
            'correo' => Session::getUserEmail(),
            'rol' => Session::getUserRole()
        ]
    ]);
} else {
    Response::error('No hay sesión activa', 401);
}
?>
