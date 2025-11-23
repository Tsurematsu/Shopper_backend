<?php
function delete_employees() {
    auth('admin');
    $errors = RequestHelper::validate(['email']);
    if ($errors) {
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }
    $email = RequestHelper::input('email');
    
    $result = Users::delete_user_by_email($email);
    
    echo json_encode([
        'success' => $result,
    ]);
    
    exit;
}
