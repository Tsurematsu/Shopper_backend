<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Carrito;
use App\Models\Producto;

class CarritoController
{
    // GET /api/carrito
    public function get(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $usuarioId = $params['usuario_id'] ?? null;

        if (!$usuarioId) {
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'usuario_id requerido']));
        }

        $items = Carrito::where('usuario_id', $usuarioId)->get();

        $resultado = [];

        foreach ($items as $item) {
            $producto = Producto::find($item->producto_id);

            if ($producto) {
                $resultado[] = [
                    'id' => $item->id,
                    'producto_id' => $producto->id,
                    'titulo' => $producto->titulo,
                    'imagenUrl' => $producto->imagen_url,
                    'precioUnitario' => $producto->precio_unitario,
                    'cantidad' => $item->cantidad,
                    'subtotal' => $producto->precio_unitario * $item->cantidad
                ];
            }
        }

        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // POST /api/carrito/add
    public function add(Request $request, Response $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['usuario_id'], $data['producto_id'], $data['cantidad'])) {
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'Datos incompletos']));
        }

        // ¿El usuario ya tiene ese producto en el carrito?
        $item = Carrito::where('usuario_id', $data['usuario_id'])
                        ->where('producto_id', $data['producto_id'])
                        ->first();

        if ($item) {
            // solo aumentamos la cantidad
            $item->cantidad += $data['cantidad'];
            $item->save();
        } else {
            // creamos desde cero
            $item = Carrito::create([
                'usuario_id' => $data['usuario_id'],
                'producto_id' => $data['producto_id'],
                'cantidad' => $data['cantidad']
            ]);
        }

        $response->getBody()->write(json_encode(['message' => 'Producto añadido', 'item' => $item]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // DELETE /api/carrito/delete
    public function delete(Request $request, Response $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['id'])) {
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'id requerido']));
        }

        $item = Carrito::find($data['id']);

        if (!$item) {
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'Item no encontrado']));
        }

        $item->delete();

        $response->getBody()->write(json_encode(['message' => 'Item eliminado']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // PUT /api/carrito/update
    public function update(Request $request, Response $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['id'], $data['cantidad'])) {
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'id y cantidad requeridos']));
        }

        $item = Carrito::find($data['id']);

        if (!$item) {
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'Item no encontrado']));
        }

        $item->cantidad = $data['cantidad'];
        $item->save();

        $response->getBody()->write(json_encode(['message' => 'Cantidad actualizada', 'item' => $item]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
