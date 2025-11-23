<?php
function auth($rol) {
    try {
        $token = JWTHelper::getTokenFromHeader(); 
        if($token=='' || $token== null) {
            echo json_encode(['message' => 'invalid access','code'=> 0]);
            exit;
        }

        $validToken = JWTHelper::validateToken($token);
        if (!$validToken) {
            echo json_encode(['message' => 'invalid access','code'=> 0]);
            exit;
        }
        

        $payload = JWTHelper::decodeToken($token);
        if ($payload['rol'] !== $rol) {
            echo json_encode(['message' => 'invalid access','code'=> 0]);
            exit;
        }
        return $payload;
    } catch (\Throwable $th) {
        echo json_encode(['message' => 'invalid access','code'=> 0]);
        exit;
    }
}