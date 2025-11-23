<?php
require __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

// Inicializar conexión
Database::init();

echo "🚀 Ejecutando migraciones...\n\n";

// Ejecutar migración de usuarios
require __DIR__ . '/migrations/CreateUsersTable.php';

echo "\n✅ Migraciones completadas\n";