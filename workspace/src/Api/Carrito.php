<?php
namespace App\Api;

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\CarritoController;

class Carrito {
     public static function getRoutes(RouteCollectorProxy $group){
        $controller = new CarritoController();

        $group->get('', [$controller, 'index']);
     } 
}