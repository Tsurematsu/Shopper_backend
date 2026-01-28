<?php
require __DIR__ . '/vendor/autoload.php';
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Middleware\JsonMiddleware;
use App\Config\Database;
use Illuminate\Database\Capsule\Manager as DB;

use App\Api\Personas;
use App\Api\Carrito;
use App\Api\Productos;
use App\Helpers\FileUploader;


Database::init();

$app = AppFactory::create();

$app->post('/api/upload', function (Request $request, Response $response) {
    try {
        $uploadedFiles = $request->getUploadedFiles();
        if (!isset($uploadedFiles['archivo'])) {throw new \Exception('No se envió ningún archivo');}
        $uploader = new FileUploader();
        $resultado = $uploader->upload($uploadedFiles['archivo']);
        $response->getBody()->write(json_encode($resultado));
        return $response;
    } catch (\Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response;
    }
});


$app->add(new JsonMiddleware());

// Ruta GET simple
$app->get('/api', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'message' => '/api => API de Shopper Funcionando correctamente ip :' . $request->getServerParams()['REMOTE_ADDR'] .' '. $request->getUri()
    ]));
    return $response;
});

$app->group('/api/personas', function ($group) {
    Personas::getRoutes($group);
});

$app->group('/api/carrito', function ($group) {
    Carrito::getRoutes($group);
});

$app->group('/api/productos', function ($group) {
    Productos::getRoutes($group);
});


$app->run();