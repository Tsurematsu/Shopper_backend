<?php
namespace App\Api;

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\ProductosController;

class Productos {
     public static function getRoutes(RouteCollectorProxy $group){
        $controller = new ProductosController();

        $group->get('', [$controller, 'index']);
        $group->post('/addproduct', [$controller, 'add']);
        $group->get('/getProductos', [$controller, 'get']);
        $group->get('/deleteProductos', [$controller, 'delete']);
        $group->get('/getProduct/{id}', [$controller, 'getOne']);
        $group->post('/updateProduct/{id}', [$controller, 'update']);
     } 
}