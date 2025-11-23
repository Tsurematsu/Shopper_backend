<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/database.php';

// Ejecutar todas las migraciones
require __DIR__ . '/migrations/CreateUsersTable.php';

echo "✅ Migraciones ejecutadas\n";