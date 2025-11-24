<?php
namespace App\Helpers;


class JWTHelper {
    private static $secretKey = null;
    private static $algorithm = 'HS256';
    private static $defaultExpiration = 5 * 3600; // 5 horas

    /**
     * Obtener la clave secreta (desde variable de entorno o valor por defecto)
     */
    private static function getSecretKey() {
        if (self::$secretKey === null) {
            self::$secretKey = getenv('SECRET_JWT') ?: "123456789";
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
     * Configurar tiempo de expiración por defecto
     */
    public static function setDefaultExpiration($seconds) {
        self::$defaultExpiration = $seconds;
    }

    /**
     * Generar un token JWT
     * 
     * @param array $payload Datos a incluir en el token (ej: ['user_id' => 1, 'email' => 'user@example.com'])
     * @param int $expiration Tiempo de expiración en segundos (opcional)
     * @return string Token JWT generado
     */
    public static function generateToken($payload, $expiration = null) {
        self::getSecretKey();
        if ($expiration === null) {
            $expiration = self::$defaultExpiration;
        }

        $issuedAt = time();
        $expire = $issuedAt + $expiration;

        // Header del JWT
        $header = [
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ];

        // Payload del JWT
        $payloadData = array_merge($payload, [
            'iat' => $issuedAt,  // Issued at
            'exp' => $expire     // Expiration time
        ]);

        // Codificar en Base64URL
        $base64UrlHeader = self::base64UrlEncode(json_encode($header));
        $base64UrlPayload = self::base64UrlEncode(json_encode($payloadData));

        // Crear la firma
        $signature = hash_hmac(
            'sha256',
            $base64UrlHeader . "." . $base64UrlPayload,
            self::$secretKey,
            true
        );
        $base64UrlSignature = self::base64UrlEncode($signature);

        // Retornar el token completo
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Validar un token JWT
     * 
     * @param string $token Token a validar
     * @return bool True si es válido, False si no lo es
     */
    public static function validateToken($token) {
        self::getSecretKey();
        try {
            $decoded = self::decodeToken($token);
            return $decoded !== null;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Decodificar y leer el contenido de un token JWT
     * 
     * @param string $token Token a decodificar
     * @return array|null Datos del payload o null si el token es inválido
     */
    public static function decodeToken($token) {
        self::getSecretKey();
        // Separar las partes del token
        $tokenParts = explode('.', $token);
        
        if (count($tokenParts) !== 3) {
            return null;
        }

        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $tokenParts;

        // Verificar la firma
        $signature = self::base64UrlDecode($base64UrlSignature);
        $expectedSignature = hash_hmac(
            'sha256',
            $base64UrlHeader . "." . $base64UrlPayload,
            self::$secretKey,
            true
        );

        if (!hash_equals($signature, $expectedSignature)) {
            return null; // Firma inválida
        }

        // Decodificar el payload
        $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);

        if (!$payload) {
            return null;
        }

        // Verificar expiración
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null; // Token expirado
        }

        return $payload;
    }

    /**
     * Obtener el token desde el header de autorización
     * 
     * @return string|null Token extraído o null si no existe
     */
    public static function getTokenFromHeader() {
        self::getSecretKey();
        $authHeader = null;
        
        // Intentar obtener el header de diferentes formas
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        } elseif (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        }
        
        // Extraer el token (remover "Bearer ")
        if ($authHeader && preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Verificar y obtener los datos del token desde el header
     * 
     * @return array|null Datos del token o null si es inválido
     */
    public static function verifyFromHeader() {
        self::getSecretKey();
        $token = self::getTokenFromHeader();
        
        if (!$token) {
            return null;
        }
        
        return self::decodeToken($token);
    }

    /**
     * Codificar en Base64URL
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decodificar desde Base64URL
     */
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Obtener tiempo restante del token en segundos
     * 
     * @param string $token Token a verificar
     * @return int|null Segundos restantes o null si el token es inválido
     */
    public static function getTimeRemaining($token) {
        $payload = self::decodeToken($token);
        
        if (!$payload || !isset($payload['exp'])) {
            return null;
        }
        
        $remaining = $payload['exp'] - time();
        return $remaining > 0 ? $remaining : 0;
    }

    /**
     * Verificar si un token está expirado
     * 
     * @param string $token Token a verificar
     * @return bool True si está expirado, False si no lo está
     */
    public static function isExpired($token) {
        $payload = self::decodeToken($token);
        
        if (!$payload || !isset($payload['exp'])) {
            return true;
        }
        
        return $payload['exp'] < time();
    }
}

?>