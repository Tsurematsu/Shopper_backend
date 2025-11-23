<?php
// RequestHelper.php

class RequestHelper {
    
    /**
     * Obtener datos JSON del body
     * @return array
     */
    public static function getJSON() {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }
    
    /**
     * Obtener un valor del JSON
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function input($key, $default = null) {
        $data = self::getJSON();
        return $data[$key] ?? $default;
    }
    
    /**
     * Validar que existan campos requeridos
     * @param array $required
     * @return array|null Array de errores o null si todo está bien
     */
    public static function validate($required = []) {
        $data = self::getJSON();
        $errors = [];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "El campo '{$field}' es requerido";
            }
        }
        
        return empty($errors) ? null : $errors;
    }
    
    /**
     * Obtener el método HTTP
     * @return string
     */
    public static function method() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    /**
     * Verificar si el método es POST
     * @return bool
     */
    public static function isPost() {
        return self::method() === 'POST';
    }
    
    /**
     * Verificar si el método es GET
     * @return bool
     */
    public static function isGet() {
        return self::method() === 'GET';
    }
}
?>