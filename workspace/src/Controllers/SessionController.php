<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SessionController
{
    public function index(Request $request, Response $response)
    {
        
        $response->getBody()->write(json_encode([
            'message' => 'Hola, esta es una prueba de la ruta /api/sessions'
        ]));
        return $response;
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


// $data = json_decode($request->getBody());
        
//         $user = User::create([
//             'name' => $data->name,
//             'email' => $data->email,
//             'hash_password' => password_hash($data->password, PASSWORD_BCRYPT),
//             'is_admin' => $data->is_admin ?? false
//         ]);