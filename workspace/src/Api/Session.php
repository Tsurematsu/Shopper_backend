<?php
namespace App\Api;

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\SessionController;

class Session {
     public static function getRoutes(RouteCollectorProxy $group){
        $controller = new SessionController();

        $group->get('', [$controller, 'index']);           // GET /api/sessions
        // $group->get('/{id}', [$controller, 'show']);       // GET /api/sessions/1
        // $group->post('', [$controller, 'store']);          // POST /api/sessions
        // $group->put('/{id}', [$controller, 'update']);     // PUT /api/sessions/1
        // $group->delete('/{id}', [$controller, 'delete']);  // DELETE /api/sessions/1
     } 
}