<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductosController
{
    public function index(Request $request, Response $response)
    {

        $response->getBody()->write(json_encode([
            'message' => 'Hola, esta es una prueba de la ruta /api/productos'
        ]));
        return $response;
    }

    public function add(Request $request, Response $response, $args)
    {
        $data = json_decode($request->getBody()->getContents());

        // Validaciones básicas
        if (!isset($data->titulo) || !isset($data->precio_unitario)) {
            $response->getBody()->write(json_encode([
                'error' => 'El campo titulo y precio_unitario son obligatorios.'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $producto = \App\Models\Producto::create([
            'titulo' => $data->titulo,
            'descripcion' => $data->descripcion ?? '',
            'imagen_url' => $data->imagen_url ?? '',
            'precio_unitario' => $data->precio_unitario,
            'costo_envio' => $data->costo_envio ?? 0,
            'cantidad' => $data->cantidad ?? 0,
            'calificacion' => $data->calificacion ?? null,
        ]);

        $response->getBody()->write(json_encode([
            'message' => 'Producto agregado exitosamente',
            'id' => $producto->id
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function get(Request $request, Response $response, $args)
    {
        $productos = \App\Models\Producto::all();

        $resultado = [];

        foreach ($productos as $p) {
            $resultado[] = [
                'id' => $p->id, // ← agregado
                'titulo' => $p->titulo,
                'descripcion' => $p->descripcion,
                'imagenUrl' => $p->imagen_url,
                'precioUnitario' => $p->precio_unitario,
                'costoEnvio' => $p->costo_envio,
                'cantidad' => $p->cantidad,
                'calificacion' => $p->calificacion
            ];
        }

        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function getOne(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;

        $producto = \App\Models\Producto::find($id);

        if (!$producto) {
            $response->getBody()->write(json_encode(false));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($producto));
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function delete(Request $request, Response $response)
    {
        $id = $request->getQueryParams()['id'] ?? null;

        if (!$id) {
            $response->getBody()->write(json_encode(['error' => 'ID requerido']));
            return $response->withStatus(400);
        }

        $producto = \App\Models\Producto::find($id);

        if (!$producto) {
            $response->getBody()->write(json_encode(false));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $producto->delete();

        $response->getBody()->write(json_encode(true));
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function update(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        $data = json_decode($request->getBody()->getContents());

        $producto = \App\Models\Producto::find($id);

        if (!$producto) {
            $response->getBody()->write(json_encode(false));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $producto->update([
            'titulo' => $data->titulo ?? $producto->titulo,
            'descripcion' => $data->descripcion ?? $producto->descripcion,
            'imagen_url' => $data->imagen_url ?? $producto->imagen_url,
            'precio_unitario' => $data->precio_unitario ?? $producto->precio_unitario,
            'costo_envio' => $data->costo_envio ?? $producto->costo_envio,
            'cantidad' => $data->cantidad ?? $producto->cantidad,
            'calificacion' => $data->calificacion ?? $producto->calificacion,
        ]);

        $response->getBody()->write(json_encode(true));
        return $response->withHeader('Content-Type', 'application/json');
    }


}


// $data = json_decode($request->getBody());

//         $user = User::create([
//             'name' => $data->name,
//             'email' => $data->email,
//             'hash_password' => password_hash($data->password, PASSWORD_BCRYPT),
//             'is_admin' => $data->is_admin ?? false
//         ]);