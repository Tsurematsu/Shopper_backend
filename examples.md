```php

Ruta con parámetros
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

```