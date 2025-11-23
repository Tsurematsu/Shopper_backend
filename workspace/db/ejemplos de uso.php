<?php
require_once 'Database.php';
require_once 'PasswordHelper.php';
require_once 'JWTHelper.php';

// ============================================
// EJEMPLO 1: SELECT - Listar todos los usuarios
// ============================================
function obtenerTodosLosUsuarios() {
    $sql = "SELECT id, email, rol, activo, fecha_creacion 
            FROM usuarios 
            ORDER BY fecha_creacion DESC";
    
    $usuarios = Database::query($sql);
    return $usuarios;
}

// Uso
$usuarios = obtenerTodosLosUsuarios();
foreach ($usuarios as $user) {
    echo "{$user['email']} - {$user['rol']}\n";
}

// ============================================
// EJEMPLO 2: SELECT con parámetros - Buscar por email
// ============================================
function buscarUsuarioPorEmail($email) {
    $sql = "SELECT * FROM usuarios WHERE email = $1";
    
    $usuario = Database::queryOne($sql, [$email]);
    return $usuario;
}

// Uso
$usuario = buscarUsuarioPorEmail('admin@ejemplo.com');
if ($usuario) {
    echo "Usuario encontrado: {$usuario['email']}\n";
} else {
    echo "Usuario no encontrado\n";
}

// ============================================
// EJEMPLO 3: INSERT - Crear usuario
// ============================================
function crearUsuario($email, $password, $rol) {
    // Hashear la contraseña
    $hashedPassword = PasswordHelper::hash($password);
    
    $sql = "INSERT INTO usuarios (email, password, rol, activo) 
            VALUES ($1, $2, $3, $4) 
            RETURNING id";
    
    try {
        // Ejecutar el INSERT
        Database::execute($sql, [$email, $hashedPassword, $rol, true]);
        
        // Obtener el ID insertado
        $userId = Database::lastInsertId();
        
        return [
            'success' => true,
            'user_id' => $userId,
            'message' => 'Usuario creado exitosamente'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Uso
$resultado = crearUsuario('nuevo@ejemplo.com', 'Password123!', 'cliente');
if ($resultado['success']) {
    echo "Usuario creado con ID: {$resultado['user_id']}\n";
}

// ============================================
// EJEMPLO 4: UPDATE - Actualizar usuario
// ============================================
function actualizarRolUsuario($userId, $nuevoRol) {
    $sql = "UPDATE usuarios SET rol = $1 WHERE id = $2";
    
    try {
        $filasAfectadas = Database::execute($sql, [$nuevoRol, $userId]);
        
        return [
            'success' => true,
            'rows_affected' => $filasAfectadas
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Uso
$resultado = actualizarRolUsuario(1, 'administrador');

// ============================================
// EJEMPLO 5: UPDATE - Cambiar contraseña
// ============================================
function cambiarPassword($userId, $nuevaPassword) {
    $hashedPassword = PasswordHelper::hash($nuevaPassword);
    
    $sql = "UPDATE usuarios SET password = $1 WHERE id = $2";
    
    Database::execute($sql, [$hashedPassword, $userId]);
}

// ============================================
// EJEMPLO 6: DELETE - Eliminar usuario (soft delete)
// ============================================
function desactivarUsuario($userId) {
    $sql = "UPDATE usuarios SET activo = FALSE WHERE id = $1";
    
    return Database::execute($sql, [$userId]);
}

// Eliminación física
function eliminarUsuario($userId) {
    $sql = "DELETE FROM usuarios WHERE id = $1";
    
    return Database::execute($sql, [$userId]);
}

// ============================================
// EJEMPLO 7: Transacciones - Operaciones múltiples
// ============================================
function transferirDatos($usuarioOrigenId, $usuarioDestinoId) {
    try {
        Database::beginTransaction();
        
        // Operación 1
        $sql1 = "UPDATE usuarios SET activo = FALSE WHERE id = $1";
        Database::execute($sql1, [$usuarioOrigenId]);
        
        // Operación 2
        $sql2 = "UPDATE usuarios SET activo = TRUE WHERE id = $1";
        Database::execute($sql2, [$usuarioDestinoId]);
        
        // Si todo salió bien, confirmar
        Database::commit();
        
        return ['success' => true];
        
    } catch (Exception $e) {
        // Si algo falló, revertir todo
        Database::rollback();
        
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// ============================================
// EJEMPLO 8: Endpoint de REGISTRO completo
// ============================================
// register.php
function endpointRegistro() {
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $rol = $data['rol'] ?? 'cliente';
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email inválido']);
        return;
    }
    
    // Validar fortaleza de contraseña
    $validation = PasswordHelper::validateStrength($password);
    if (!$validation['valid']) {
        http_response_code(400);
        echo json_encode(['errors' => $validation['errors']]);
        return;
    }
    
    // Verificar si el email ya existe
    $sqlCheck = "SELECT id FROM usuarios WHERE email = $1";
    $existe = Database::queryOne($sqlCheck, [$email]);
    
    if ($existe) {
        http_response_code(409);
        echo json_encode(['error' => 'El email ya está registrado']);
        return;
    }
    
    // Crear usuario
    try {
        $hashedPassword = PasswordHelper::hash($password);
        
        $sql = "INSERT INTO usuarios (email, password, rol, activo) 
                VALUES ($1, $2, $3, TRUE) 
                RETURNING id, email, rol, fecha_creacion";
        
        Database::execute($sql, [$email, $hashedPassword, $rol]);
        $userId = Database::lastInsertId();
        
        // Buscar el usuario recién creado
        $nuevoUsuario = Database::queryOne(
            "SELECT id, email, rol, fecha_creacion FROM usuarios WHERE id = $1",
            [$userId]
        );
        
        // Generar token JWT
        $token = JWTHelper::generateToken([
            'user_id' => $nuevoUsuario['id'],
            'email' => $nuevoUsuario['email'],
            'rol' => $nuevoUsuario['rol']
        ]);
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'token' => $token,
            'user' => $nuevoUsuario
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear usuario: ' . $e->getMessage()]);
    }
}

// ============================================
// EJEMPLO 9: Endpoint de LOGIN completo
// ============================================
// login.php
function endpointLogin() {
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email y contraseña son requeridos']);
        return;
    }
    
    // Buscar usuario
    $sql = "SELECT * FROM usuarios WHERE email = $1";
    $usuario = Database::queryOne($sql, [$email]);
    
    if (!$usuario) {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales inválidas']);
        return;
    }
    
    // Verificar si está activo
    if (!$usuario['activo']) {
        http_response_code(403);
        echo json_encode(['error' => 'Usuario inactivo']);
        return;
    }
    
    // Verificar contraseña
    if (!PasswordHelper::verify($password, $usuario['password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales inválidas']);
        return;
    }
    
    // Generar token JWT
    $token = JWTHelper::generateToken([
        'user_id' => $usuario['id'],
        'email' => $usuario['email'],
        'rol' => $usuario['rol']
    ]);
    
    // No enviar la contraseña al cliente
    unset($usuario['password']);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'token' => $token,
        'user' => $usuario
    ]);
}

// ============================================
// EJEMPLO 10: Endpoint protegido con autenticación
// ============================================
// perfil.php
function endpointPerfil() {
    header('Content-Type: application/json');
    
    // Verificar token
    $tokenData = JWTHelper::verifyFromHeader();
    
    if (!$tokenData) {
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        return;
    }
    
    // Obtener datos del usuario
    $sql = "SELECT id, email, rol, activo, fecha_creacion 
            FROM usuarios 
            WHERE id = $1";
    
    $usuario = Database::queryOne($sql, [$tokenData['user_id']]);
    
    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'user' => $usuario
    ]);
}

// ============================================
// EJEMPLO 11: Búsqueda con filtros
// ============================================
function buscarUsuarios($filtros = []) {
    $sql = "SELECT id, email, rol, activo, fecha_creacion FROM usuarios WHERE 1=1";
    $params = [];
    $paramCount = 1;
    
    if (!empty($filtros['rol'])) {
        $sql .= " AND rol = $" . $paramCount++;
        $params[] = $filtros['rol'];
    }
    
    if (isset($filtros['activo'])) {
        $sql .= " AND activo = $" . $paramCount++;
        $params[] = $filtros['activo'];
    }
    
    if (!empty($filtros['busqueda'])) {
        $sql .= " AND email ILIKE $" . $paramCount++;
        $params[] = '%' . $filtros['busqueda'] . '%';
    }
    
    $sql .= " ORDER BY fecha_creacion DESC";
    
    if (!empty($filtros['limite'])) {
        $sql .= " LIMIT $" . $paramCount++;
        $params[] = $filtros['limite'];
    }
    
    return Database::query($sql, $params);
}

// Uso
$usuarios = buscarUsuarios([
    'rol' => 'cliente',
    'activo' => true,
    'busqueda' => 'ejemplo',
    'limite' => 10
]);

// ============================================
// EJEMPLO 12: Paginación
// ============================================
function obtenerUsuariosPaginados($pagina = 1, $porPagina = 10) {
    $offset = ($pagina - 1) * $porPagina;
    
    // Contar total
    $sqlCount = "SELECT COUNT(*) as total FROM usuarios";
    $totalResult = Database::queryOne($sqlCount);
    $total = $totalResult['total'];
    
    // Obtener datos
    $sql = "SELECT id, email, rol, activo, fecha_creacion 
            FROM usuarios 
            ORDER BY fecha_creacion DESC 
            LIMIT $1 OFFSET $2";
    
    $usuarios = Database::query($sql, [$porPagina, $offset]);
    
    return [
        'data' => $usuarios,
        'pagination' => [
            'total' => $total,
            'per_page' => $porPagina,
            'current_page' => $pagina,
            'total_pages' => ceil($total / $porPagina)
        ]
    ];
}

// Uso
$resultado = obtenerUsuariosPaginados(1, 20);
echo "Total de usuarios: {$resultado['pagination']['total']}\n";

// ============================================
// EJEMPLO 13: Verificar conexión
// ============================================
if (Database::testConnection()) {
    echo "✓ Conexión exitosa a la base de datos\n";
} else {
    echo "✗ Error de conexión\n";
}

// ============================================
// EJEMPLO 14: Cerrar conexión (opcional)
// ============================================
Database::close();

?>