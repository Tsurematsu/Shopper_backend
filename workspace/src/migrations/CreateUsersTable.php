<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('users', function ($table) {
    $table->id();                          // SERIAL PRIMARY KEY
    $table->string('name');                // VARCHAR(255)
    $table->string('email')->unique();     // VARCHAR(255) UNIQUE
    $table->string('password');
    $table->integer('age')->nullable();    // Columna opcional
    $table->boolean('active')->default(true);
    $table->timestamps();                  // created_at, updated_at
});

Capsule::schema()->create('products', function ($table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 10, 2);
    $table->integer('stock')->default(0);
    $table->foreignId('user_id')->constrained(); // FK a users
    $table->timestamps();
});