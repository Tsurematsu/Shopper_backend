<?php
require __DIR__ . '/vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = AppFactory::create();

// Ruta GET simple
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode(['message' => 'Hola Mundo']));
    return $response->withHeader('Content-Type', 'application/json');
});

// Ruta con parámetros
$app->get('/users/{id}', function (Request $request, Response $response, $args) {
    $userId = $args['id'];
    $response->getBody()->write(json_encode(['userId' => $userId]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Ruta POST
$app->post('/users', function (Request $request, Response $response) {
    $data = json_decode($request->getBody(), true);
    $response->getBody()->write(json_encode(['received' => $data]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});

$app->run();