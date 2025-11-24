<?php
namespace App\Api;

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\ProductosController;

class Productos {
     public static function getRoutes(RouteCollectorProxy $group){
        $controller = new ProductosController();

        $group->get('', [$controller, 'index']);           // GET /api/users
        // $group->get('/{id}', [$controller, 'show']);       // GET /api/users/1
        // $group->post('', [$controller, 'store']);          // POST /api/users
        // $group->put('/{id}', [$controller, 'update']);     // PUT /api/users/1
        // $group->delete('/{id}', [$controller, 'delete']);  // DELETE /api/users/1
     } 
}