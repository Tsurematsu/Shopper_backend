<?php
namespace App\Controllers;

use App\Models\Persona;
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

    public function registrarPersona(Request $request, Response $response, $args)
    {
        // Obtener JSON del request
        $data = json_decode($request->getBody()->getContents());

        // Validaciones mínimas
        if (!isset($data->email) || !isset($data->usuario) || !isset($data->contrasena)) {
            $response->getBody()->write(json_encode([
                'error' => 'Los campos email, usuario y contrasena son obligatorios.'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Validar email repetido
        if (Persona::where('email', $data->email)->exists()) {
            $response->getBody()->write(json_encode([
                'error' => 'El email ya está registrado.'
            ]));
            return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
        }

        // Validar usuario repetido
        if (Persona::where('usuario', $data->usuario)->exists()) {
            $response->getBody()->write(json_encode([
                'error' => 'El usuario ya está registrado.'
            ]));
            return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
        }

        // Crear persona
        $persona = Persona::create([
            'email'      => $data->email,
            'usuario'    => $data->usuario,
            'contrasena' => password_hash($data->contrasena, PASSWORD_BCRYPT),
            'is_admin'   => $data->is_admin ?? false
        ]);

        // Respuesta de éxito
        $response->getBody()->write(json_encode([
            'message' => 'Persona registrada exitosamente.',
            'persona' => [
                'id'      => $persona->id,
                'email'   => $persona->email,
                'usuario' => $persona->usuario,
                'is_admin'=> $persona->is_admin
            ]
        ]));

        return $response;
    }

    public function login(Request $request, Response $response, $args)
    {
        $data = json_decode($request->getBody()->getContents());
        // Validación de datos
        if (!isset($data->usuario) || !isset($data->contrasena)) {
            $response->getBody()->write(json_encode([
                'error' => 'Debe enviar usuario y contrasena.'
            ]));
            return $response;
        }

        // Buscar por usuario
        $persona = \App\Models\Persona::where('usuario', $data->usuario)->first();

        if (!$persona) {
            // Usuario no encontrado
            $response->getBody()->write(json_encode(["error"=>false]));
            return $response;
        }

        // Verificar contraseña
        if (!password_verify($data->contrasena, $persona->contrasena)) {
            // Contraseña incorrecta
            $response->getBody()->write(json_encode(["error"=>false]));
            return $response;
        }

        // Login correcto → retornar el ID
        $response->getBody()->write(json_encode([
            'id' => $persona->id
        ]));

        return $response;
    }

}



// $data = json_decode($request->getBody());
        
//         $user = User::create([
//             'name' => $data->name,
//             'email' => $data->email,
//             'hash_password' => password_hash($data->password, PASSWORD_BCRYPT),
//             'is_admin' => $data->is_admin ?? false
//         ]);