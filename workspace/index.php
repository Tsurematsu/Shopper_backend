<?php
require __DIR__ . '/vendor/autoload.php';


use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Config\Database;
use Illuminate\Database\Capsule\Manager as DB;

Database::init();

$app = AppFactory::create();

// Ruta GET simple
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode(['message' => 'Hola a todo el Mundo']));
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

$app->get('/api/health', function (Request $request, Response $response) {
    try {
        // Intentar una query simple
        DB::connection()->getPdo();
        $dbStatus = 'connected';
        
        // Query de prueba opcional
        $result = DB::select('SELECT version()');
        $version = $result[0]->version ?? 'unknown';
        
        $data = [
            'status' => 'ok',
            'database' => $dbStatus,
            'postgres_version' => $version,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
        
    } catch (\Exception $e) {
        $data = [
            'status' => 'error',
            'database' => 'disconnected',
            'error' => $e->getMessage()
        ];
        
        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

$app->run();