<?php
function login() {
    $errors = RequestHelper::validate(['email', 'password']);
    if ($errors) {
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }
    
    $email = RequestHelper::input('email');
    $password = RequestHelper::input('password');
    
    // $data = RequestHelper::getJSON();
    
    $usuario = Users::get_user_by_email($email);
    $validate = PasswordHelper::verify(
        $password, 
        $usuario['password']
    );
    
    if($validate){
        $userData = [
            'id' => $usuario['id'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol'],
            'activo' => $usuario['activo']
        ];
        $token = JWTHelper::generateToken($userData);
        echo json_encode([
            'success' => $validate,
            'token' => $token,
            'user' =>  $userData
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => $validate,
        'message' => "Invalid user"
    ]);
    exit;
}