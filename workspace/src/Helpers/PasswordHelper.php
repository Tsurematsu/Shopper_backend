<?php
namespace App\Helpers;


class PasswordHelper {
    private static $secretKey = null;
    private static $algorithm = 'sha256'; // Algoritmo para HMAC
    
    /**
     * Obtener la clave secreta (desde variable de entorno o valor por defecto)
     */
    private static function getSecretKey() {
        if (self::$secretKey === null) {
            self::$secretKey = getenv('KEY_HASH') ?: "default_key_hash_123";
        }
        return self::$secretKey;
    }

    /**
     * Configurar la clave secreta manualmente
     */
    public static function setSecretKey($key) {
        self::$secretKey = $key;
    }

    /**
     * Hashear una contraseña usando password_hash() + HMAC
     * Este método combina bcrypt (password_hash) con HMAC para mayor seguridad
     * 
     * @param string $password Contraseña en texto plano
     * @return string Hash de la contraseña
     */
    public static function hash($password) {
        // Primero aplicamos HMAC con nuestra clave secreta
        $hmac = hash_hmac(self::$algorithm, $password, self::getSecretKey());
        
        // Luego hasheamos el resultado con bcrypt
        return password_hash($hmac, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verificar si una contraseña coincide con un hash
     * 
     * @param string $password Contraseña en texto plano
     * @param string $hash Hash almacenado en la base de datos
     * @return bool True si coincide, False si no
     */
    public static function verify($password, $hash) {
        // Aplicamos el mismo HMAC a la contraseña
        $hmac = hash_hmac(self::$algorithm, $password, self::getSecretKey());
        
        // Verificamos con password_verify
        return password_verify($hmac, $hash);
    }

    /**
     * Verificar si un hash necesita ser regenerado (rehashed)
     * Útil si cambias el cost de bcrypt
     * 
     * @param string $hash Hash a verificar
     * @return bool True si necesita rehash, False si no
     */
    public static function needsRehash($hash) {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Generar una contraseña aleatoria segura
     * 
     * @param int $length Longitud de la contraseña (mínimo 8)
     * @param bool $includeSpecialChars Incluir caracteres especiales
     * @return string Contraseña generada
     */
    public static function generateSecurePassword($length = 12, $includeSpecialChars = true) {
        if ($length < 8) {
            $length = 8;
        }

        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%^&*()-_=+[]{}|;:,.<>?';

        $chars = $lowercase . $uppercase . $numbers;
        if ($includeSpecialChars) {
            $chars .= $special;
        }

        $password = '';
        $charsLength = strlen($chars);

        // Asegurar al menos un carácter de cada tipo
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        
        if ($includeSpecialChars) {
            $password .= $special[random_int(0, strlen($special) - 1)];
        }

        // Completar el resto de la longitud
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $chars[random_int(0, $charsLength - 1)];
        }

        // Mezclar los caracteres
        return str_shuffle($password);
    }

    /**
     * Validar la fortaleza de una contraseña
     * 
     * @param string $password Contraseña a validar
     * @param int $minLength Longitud mínima requerida
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateStrength($password, $minLength = 8) {
        $errors = [];

        if (strlen($password) < $minLength) {
            $errors[] = "La contraseña debe tener al menos {$minLength} caracteres";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Debe contener al menos una letra minúscula";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Debe contener al menos una letra mayúscula";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Debe contener al menos un número";
        }

        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = "Debe contener al menos un carácter especial";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Generar un token de recuperación de contraseña
     * 
     * @param int $userId ID del usuario
     * @param int $expiration Tiempo de expiración en segundos (default: 1 hora)
     * @return string Token de recuperación
     */
    public static function generateRecoveryToken($userId, $expiration = 3600) {
        $data = $userId . '|' . (time() + $expiration) . '|' . bin2hex(random_bytes(16));
        return hash_hmac(self::$algorithm, $data, self::getSecretKey()) . '.' . base64_encode($data);
    }

    /**
     * Validar un token de recuperación de contraseña
     * 
     * @param string $token Token a validar
     * @return array|null ['user_id' => int, 'expires' => int] o null si es inválido
     */
    public static function validateRecoveryToken($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 2) {
            return null;
        }

        list($hash, $encodedData) = $parts;
        $data = base64_decode($encodedData);
        
        // Verificar la firma
        $expectedHash = hash_hmac(self::$algorithm, $data, self::getSecretKey());
        if (!hash_equals($hash, $expectedHash)) {
            return null;
        }

        // Extraer los datos
        $dataParts = explode('|', $data);
        if (count($dataParts) !== 3) {
            return null;
        }

        list($userId, $expireTime, $random) = $dataParts;

        // Verificar expiración
        if (time() > $expireTime) {
            return null;
        }

        return [
            'user_id' => (int)$userId,
            'expires' => (int)$expireTime
        ];
    }

    /**
     * Comparar contraseñas de forma segura (timing-safe)
     * 
     * @param string $password1 Primera contraseña
     * @param string $password2 Segunda contraseña
     * @return bool True si son iguales
     */
    public static function secureCompare($password1, $password2) {
        return hash_equals($password1, $password2);
    }
}

?>