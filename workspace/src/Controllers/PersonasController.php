<?php
namespace App\Controllers;

use App\Models\Persona;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PersonasController
{
    public function index(Request $request, Response $response)
    {
        $response->getBody()->write(json_encode("Endtpoint funcionando"));
        return $response;
    }

    public function registrarPersona(Request $request, Response $response, $args)
    {
        // Obtener JSON del request
        $data = json_decode($request->getBody()->getContents());

        // Validaciones mínimas
        if (
            !isset($data->email) || empty(trim($data->email)) ||
            !isset($data->usuario) || empty(trim($data->usuario)) ||
            !isset($data->contrasena) || empty(trim($data->contrasena))
        ) {
            $response->getBody()->write(json_encode([
                'error' => 'Los campos email, usuario y contrasena son obligatorios y no pueden estar vacíos.'
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
            'id' => $persona->id,
            'email'=> $persona->email,
            'usuario' => $persona->usuario,
            'isAdmin' => $persona->is_admin,
        ]));

        return $response;
    }

    // Método para obtener todas las personas
    public function obtenerTodas(Request $request, Response $response)
    {
        // Eloquent: Obtener todos los registros
        $personas = Persona::all();

        // Escribir respuesta
        $response->getBody()->write(json_encode($personas));
        
        // Es buena práctica añadir el header Content-Type
        return $response;
    }

    // Método para obtener una persona por ID
    public function obtenerPorId(Request $request, Response $response, $args)
    {
        // Obtener el ID de la URL (definido en la ruta como {id})
        $id = $args['id'];

        // Eloquent: Buscar por ID
        $persona = Persona::find($id);

        // Validar si existe
        if (!$persona) {
            $response->getBody()->write(json_encode([
                'error' => 'Persona no encontrada'
            ]));
            return $response;
        }

        // Si existe, devolver el objeto
        $response->getBody()->write(json_encode($persona));
        
        return $response;
    }

    public function actualizarPersona(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        
        // 1. Buscar la persona
        $persona = Persona::find($id);

        if (!$persona) {
            $response->getBody()->write(json_encode(['error' => 'Persona no encontrada']));
            return $response;
        }

        // 2. Obtener datos del body
        $data = json_decode($request->getBody()->getContents());

        // --- ACTUALIZAR USUARIO (si se envía) ---
        if (isset($data->usuario) && !empty(trim($data->usuario))) {
            // Si el usuario es diferente al actual, verificar que no esté repetido en la BD
            if ($data->usuario !== $persona->usuario) {
                if (Persona::where('usuario', $data->usuario)->exists()) {
                    $response->getBody()->write(json_encode(['error' => 'El nombre de usuario ya está en uso.']));
                    return $response;
                }
                $persona->usuario = trim($data->usuario);
            }
        }

        // --- ACTUALIZAR EMAIL (si se envía) ---
        if (isset($data->email) && !empty(trim($data->email))) {
            // Si el email es diferente al actual, verificar duplicados
            if ($data->email !== $persona->email) {
                if (Persona::where('email', $data->email)->exists()) {
                    $response->getBody()->write(json_encode(['error' => 'El email ya está registrado por otra persona.']));
                    return $response;
                }
                $persona->email = trim($data->email);
            }
        }

        // --- ACTUALIZAR CONTRASEÑA ---
        // Requiere enviar "contrasena_actual" y "nueva_contrasena"
        if (isset($data->contrasena_actual) && isset($data->nueva_contrasena)) {
            // Verificar que no estén vacías
            if (!empty(trim($data->contrasena_actual)) && !empty(trim($data->nueva_contrasena))) {
                
                // 1. Validar que la contraseña actual sea correcta
                if (!password_verify($data->contrasena_actual, $persona->contrasena)) {
                    $response->getBody()->write(json_encode(['error' => 'La contraseña actual es incorrecta.']));
                    return $response;
                }

                // 2. Encriptar y guardar la nueva
                $persona->contrasena = password_hash($data->nueva_contrasena, PASSWORD_BCRYPT);
            }
        }

        // --- ACTUALIZAR ADMIN (Opcional) ---
        if (isset($data->is_admin)) {
            $persona->is_admin = $data->is_admin;
        }

        // 3. Guardar cambios en la base de datos
        // Eloquent es inteligente: si no hubo cambios, save() no hace nada.
        $persona->save();

        $response->getBody()->write(json_encode([
            'message' => 'Persona actualizada exitosamente',
            'persona' => $persona
        ]));

        return $response;
    }

    public function eliminarPersona(Request $request, Response $response, $args)
    {
        $id = $args['id'];

        // 1. Buscar la persona
        $persona = Persona::find($id);

        // 2. Validar si existe
        if (!$persona) {
            $response->getBody()->write(json_encode([
                'error' => 'Persona no encontrada, no se pudo eliminar.'
            ]));
            return $response;
        }

        // 3. Eliminar el registro
        $persona->delete();

        // 4. Respuesta de éxito
        $response->getBody()->write(json_encode([
            'message' => 'Persona eliminada exitosamente.',
            'id_eliminado' => $id
        ]));

        return $response;
    }

}