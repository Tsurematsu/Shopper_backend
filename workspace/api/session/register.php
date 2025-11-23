<?php
function register() {
    auth('admin');

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
    
    $result = Users::add_user_employee(
        $email, 
        $password
    );
    
    echo json_encode([
        'success' => $result,
    ]);
    
    exit;
}