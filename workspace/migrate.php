<?php
require __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

// Inicializar conexión
Database::init();

echo "🚀 Ejecutando migraciones...\n\n";



echo "\n✅ Migraciones completadas\n";