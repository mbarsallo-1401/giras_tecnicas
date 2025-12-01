
<?php
// Configuración de sesiones
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Session {
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public static function get($key) {
        return $_SESSION[$key] ?? null;
    }
    
    public static function exists($key) {
        return isset($_SESSION[$key]);
    }
    
    public static function delete($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    public static function destroy() {
        session_destroy();
    }
    
    public static function isAuthenticated() {
        return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_rol']);
    }
    
    public static function getUserId() {
        return $_SESSION['usuario_id'] ?? null;
    }
    
    public static function getUserRole() {
        return $_SESSION['usuario_rol'] ?? null;
    }
    
    public static function getUserEmail() {
        return $_SESSION['usuario_correo'] ?? null;
    }
    
    public static function getUserName() {
        return $_SESSION['usuario_nombre'] ?? null;
    }
}
?>
