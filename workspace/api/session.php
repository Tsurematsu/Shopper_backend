<?php
//helpers
require_once '../db/conexion.php';
require_once '../helpers/PasswordHelper.php';
require_once '../helpers/RequestHelper.php';
require_once '../helpers/JWTHelper.php';

//controllers
require_once './controllers/Users.php';

//Middlewares
require_once './middlewares/auth.php';
require_once './middlewares/preventAdmin.php';

//points
require_once './session/login.php';
require_once './session/register.php';
require_once './session/employees.php';
require_once './session/delete_employees.php';

header('Content-Type: application/json');
preventAdmin();

function session_router() {
    $point = $_GET['action'] ?? null;
    if (empty($point)){echo json_encode(['message' => 'error not action method' ]); exit;}

    if (RequestHelper::isPost()) {
        switch ($point) {
            case 'login':
                login();
                break;
            case 'register':
                register();
                break;
            case 'deleteEmployee':
                delete_employees();
                break;
            default:
                break;
        }
    }
    
    if (RequestHelper::isGet()) {
        switch ($point) {
            case 'employees':
                employees();
                break;
            default:
                break;
        }
    }

    echo json_encode([
        'message' => 'error access not found' 
    ]);
}
session_router();