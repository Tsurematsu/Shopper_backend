<?php
require_once './db/conexion.php';
require_once './helpers/JWTHelper.php';

header('Content-Type: application/json');

// $userData = [
//     'user_id' => 123,
//     'email' => 'usuario@ejemplo.com',
//     'role' => 'admin'
// ];

echo json_encode([
    'success' => true,
    'message' => "API RUNNING"
]);


// $token = JWTHelper::getTokenFromHeader(); 
// $payload = JWTHelper::decodeToken($token);
// if ($payload) {
//     echo json_encode([
//         'success' => true,
//         'message' => $payload['email']
//     ]);
// }


// $token = JWTHelper::getTokenFromHeader(); 
// if (JWTHelper::validateToken($token)) {
//     echo json_encode([
//         'success' => true,
//         'message' => 'Token valido'
//     ]);
// } else {
//     echo json_encode([
//         'success' => false,
//         'message' => 'Token invalido'
//     ]);
// }


// $token = JWTHelper::generateToken($userData);
// echo json_encode([
//     'success' => true,
//     'message' => 'Token generado',
//     'data' => $token 
// ]);


// if (Database::testConnection()) {
//     echo "¡Conexión exitosa a PostgreSQL!";
// } else {
//     echo "Error al conectar";
// }
?>