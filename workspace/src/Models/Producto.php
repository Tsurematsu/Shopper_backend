<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'imagen_url',
        'precio_unitario',
        'costo_envio',
        'cantidad',
        'calificacion'
    ];

    // Mapeo porque en SQL son creado_en / actualizado_en
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
}
