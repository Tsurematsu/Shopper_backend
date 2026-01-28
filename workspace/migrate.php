<?php
require __DIR__ . '/vendor/autoload.php';

use App\Config\Database;
use Illuminate\Database\Capsule\Manager as DB;

// Inicializar conexión
Database::init();

echo "🚀 Ejecutando migraciones SQL...\n\n";

// Obtener todos los archivos .sql de la carpeta migrations
$migrationsPath = __DIR__ . '/Migrations';
$sqlFiles = glob($migrationsPath . '/*.sql');

// Ordenar archivos alfabéticamente
sort($sqlFiles);

if (empty($sqlFiles)) {
    echo "⚠️  No se encontraron archivos .sql en /Migrations\n";
    exit(0);
}

foreach ($sqlFiles as $file) {
    $filename = basename($file);
    echo "📄 Ejecutando: {$filename}...\n";
    
    try {
        // Leer contenido del archivo
        $sql = file_get_contents($file);
        
        // Ejecutar el SQL
        DB::connection()->getPdo()->exec($sql);
        
        echo "   ✅ {$filename} ejecutado correctamente\n\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Error en {$filename}: {$e->getMessage()}\n\n";
        exit(1);
    }
}

echo "✅ Todas las migraciones completadas\n";