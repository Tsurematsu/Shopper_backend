<?php
function employees()  {
    auth('admin');
    $result = Users::get_all_users_employees();
    echo json_encode([
        'success' => $result,
    ]);
    exit;
}