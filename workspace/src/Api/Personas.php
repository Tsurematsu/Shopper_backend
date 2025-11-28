<?php
namespace App\Api;

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\PersonasController;

class Personas {
     public static function getRoutes(RouteCollectorProxy $group){
        $controller = new PersonasController();
        $group->get('/test', [$controller, 'index']);
        $group->post('/register', [$controller, 'registrarPersona']);
        $group->post('/login', [$controller, 'login']);
        $group->get('/obtenerPersona/{id}', [$controller, 'obtenerPorId']);
     } 
}