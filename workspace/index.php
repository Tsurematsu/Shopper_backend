<?php
require __DIR__ . '/vendor/autoload.php';
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Config\Database;
use App\Api\Personas;
use App\Middleware\JsonMiddleware;

Database::init();

$app = AppFactory::create();
$app->add(new JsonMiddleware());

// Ruta GET simple
$app->get('/', function (Request $request, Response $response) {
    return $response->getBody()->write(json_encode([
        'message' => 'Hola a todo el Mundo'
    ]));
});

// Cargar rutas de Personas usando el método estático
$app->group('/api/personas', function ($group) {
    Personas::getRoutes($group);
});


$app->run();