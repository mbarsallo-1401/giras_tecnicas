
<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../utils/response.php';

Session::destroy();
Response::success('Sesión cerrada exitosamente');
?>
