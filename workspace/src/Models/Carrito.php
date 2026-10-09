<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $table = 'carrito';

    protected $fillable = [
        'titulo',
        'cantidad_seleccionada',
        'costo_envio',
        'precio_unitario',
        'imagen_url',
        'cliente_id'
    ];

    // Mapeo timestamps personalizados
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
}
