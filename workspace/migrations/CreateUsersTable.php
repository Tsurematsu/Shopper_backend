<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('users');

Capsule::schema()->create('users', function ($table) {
    $table->id();                               // ID autoincremental
    $table->string('name');                     // Nombre del usuario
    $table->string('email')->unique();          // Email único
    $table->string('hash_password');            // Contraseña hasheada
    $table->boolean('is_admin')->default(false); // Admin (default: false)
    $table->timestamps();                       // created_at, updated_at
});

echo "✅ Tabla 'users' creada\n";