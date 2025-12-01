
<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../utils/response.php';

class AuthMiddleware {
    public static function requireAuth() {
        if (!Session::isAuthenticated()) {
            Response::unauthorized('Debe iniciar sesión para acceder a este recurso');
        }
    }
    
    public static function requireRole($role) {
        self::requireAuth();
        $userRole = Session::getUserRole();
        
        if ($userRole !== $role) {
            Response::forbidden('No tiene permisos para acceder a este recurso');
        }
    }
    
    public static function requireAnyRole($roles) {
        self::requireAuth();
        $userRole = Session::getUserRole();
        
        if (!in_array($userRole, $roles)) {
            Response::forbidden('No tiene permisos para acceder a este recurso');
        }
    }
}
?>
