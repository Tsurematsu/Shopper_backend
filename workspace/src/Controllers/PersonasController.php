<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PersonasController
{
    public function index(Request $request, Response $response)
    {
        $response->getBody()->write(json_encode([
            'message' => 'Hola, esta es una prueba de la ruta /api/personas'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, $args)
    {

    }

    public function store(Request $request, Response $response)
    {
        
    }

    public function update(Request $request, Response $response, $args)
    {
    }

    public function delete(Request $request, Response $response, $args)
    {
    }
}