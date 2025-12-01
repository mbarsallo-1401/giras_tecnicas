
<?php
// Funciones de validación
class Validator {
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function required($value) {
        return !empty(trim($value));
    }
    
    public static function minLength($value, $min) {
        return strlen($value) >= $min;
    }
    
    public static function maxLength($value, $max) {
        return strlen($value) <= $max;
    }
    
    public static function sanitize($value) {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }
    
    public static function sanitizeArray($array) {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::sanitizeArray($value);
            } else {
                $result[$key] = self::sanitize($value);
            }
        }
        return $result;
    }
}
?>
