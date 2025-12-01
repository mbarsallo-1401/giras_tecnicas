
<?php
// Utilidades para respuestas JSON
class Response {
    public static function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    public static function success($message, $data = null, $statusCode = 200) {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
    
    public static function error($message, $statusCode = 400) {
        self::json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }
    
    public static function unauthorized($message = 'No autorizado') {
        self::error($message, 401);
    }
    
    public static function forbidden($message = 'Acceso prohibido') {
        self::error($message, 403);
    }
    
    public static function notFound($message = 'Recurso no encontrado') {
        self::error($message, 404);
    }
}
?>
