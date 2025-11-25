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
            [
                'titulo' => 'Kit Pesas 20 Mancuernas Magnux KG',
                'descripcion' => 'Ahora realizar tu rutina de ejercicio será más fácil, ya que cuentas con uno de los mejores set de mancuernas calidad/precio.',
                'imgenUrl' => 'https://http2.mlstatic.com/D_NQ_NP_2X_750964-MLA95494678340_102025-F.webp',
                'precioUnitairo' => '10000',
                'costoEnvio' => '110000',
                'cantidad' => '3',
                'calificacion' => '4'
            ],
            [
                'titulo' => 'Colchoneta Magnux Yoga Pilates Mat Tapete Ejercicios 10mm De Grosor',
                'descripcion' => 'Ideal para sesiones de yoga, pilates, estiramientos, meditación y otros ejercicios de fortalecimiento.',
                'imgenUrl' => 'https://http2.mlstatic.com/D_Q_NP_859732-MLA95671233684_102025-F.webp',
                'precioUnitairo' => '12000',
                'costoEnvio' => '11000',
                'cantidad' => '3',
                'calificacion' => '4.5'
            ]
        ]));
        return $response;
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
            return $response;
        }

        // Validar email repetido
        if (Persona::where('email', $data->email)->exists()) {
            $response->getBody()->write(json_encode([
                'error' => 'El email ya está registrado.'
            ]));
            return $response;
        }

        // Validar usuario repetido
        if (Persona::where('usuario', $data->usuario)->exists()) {
            $response->getBody()->write(json_encode([
                'error' => 'El usuario ya está registrado.'
            ]));
            return $response;
        }

        // Crear persona
        $persona = Persona::create([
            'email' => $data->email,
            'usuario' => $data->usuario,
            'contrasena' => password_hash($data->contrasena, PASSWORD_BCRYPT),
            'is_admin' => $data->is_admin ?? false
        ]);

        // Respuesta de éxito
        $response->getBody()->write(json_encode([
            'message' => 'Persona registrada exitosamente.',
            'persona' => [
                'id' => $persona->id,
                'email' => $persona->email,
                'usuario' => $persona->usuario,
                'is_admin' => $persona->is_admin
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
            $response->getBody()->write(json_encode(["error" => false]));
            return $response;
        }

        // Verificar contraseña
        if (!password_verify($data->contrasena, $persona->contrasena)) {
            // Contraseña incorrecta
            $response->getBody()->write(json_encode(["error" => false]));
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