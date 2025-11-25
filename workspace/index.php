<?php
require __DIR__ . '/vendor/autoload.php';
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Middleware\JsonMiddleware;
use App\Config\Database;
use Illuminate\Database\Capsule\Manager as DB;

use App\Api\Personas;
use App\Api\Session;
use App\Api\Carrito;
use App\Api\Productos;
use App\Helpers\FileUploader;


Database::init();

$app = AppFactory::create();

$app->post('/api/upload', function (Request $request, Response $response) {
    try {
        $uploadedFiles = $request->getUploadedFiles();
        
        if (!isset($uploadedFiles['archivo'])) {
            throw new \Exception('No se envió ningún archivo');
        }
        
        $uploader = new FileUploader();
        $resultado = $uploader->upload($uploadedFiles['archivo']);
        
        // Aquí puedes guardar la info en la base de datos
        // DB::table('archivos')->insert([
        //     'nombre_original' => $resultado['original_name'],
        //     'nombre_guardado' => $resultado['filename'],
        //     'ruta' => $resultado['path'],
        //     'created_at' => date('Y-m-d H:i:s')
        // ]);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $resultado
        ]));
        
        return $response;
        
    } catch (\Exception $e) {
        
        $response->getBody()->write(json_encode([
            'error' => $e->getMessage()
        ]));

        return $response->withStatus(400);
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

$app->get('/api/testProducts', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        [
            'titulo'=>'Kit Pesas 20 Mancuernas Magnux KG',
            'descripcion'=>'Ahora realizar tu rutina de ejercicio será más fácil, ya que cuentas con uno de los mejores set de mancuernas calidad/precio.',
            'imgenUrl' =>'https://http2.mlstatic.com/D_NQ_NP_2X_750964-MLA95494678340_102025-F.webp',
            'precioUnitairo' =>'10000',
            'costoEnvio' =>'110000',
            'cantidad' =>'3',
            'calificacion' =>'4'
        ],
        [
            'titulo'=>'Colchoneta Magnux Yoga Pilates Mat Tapete Ejercicios 10mm De Grosor',
            'descripcion'=>'Ideal para sesiones de yoga, pilates, estiramientos, meditación y otros ejercicios de fortalecimiento.',
            'imgenUrl' =>'https://http2.mlstatic.com/D_Q_NP_859732-MLA95671233684_102025-F.webp',
            'precioUnitairo' =>'12000',
            'costoEnvio' =>'11000',
            'cantidad' =>'3',
            'calificacion' =>'4.5'
        ]
    ]));
    return $response;
});

// Cargar rutas de Personas usando el método estático
$app->group('/api/personas', function ($group) {
    Personas::getRoutes($group);
});

$app->group('/api/session', function ($group) {
    Session::getRoutes($group);
});

$app->group('/api/carrito', function ($group) {
    Carrito::getRoutes($group);
});

$app->group('/api/productos', function ($group) {
    Productos::getRoutes($group);
});


$app->run();