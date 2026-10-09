<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
        'email',
        'usuario',
        'contrasena',
        'is_admin'
    ];

    // Si no quieres exponer la contraseña en JSON
    protected $hidden = ['contrasena'];

    // Mapeo timestamps SQL → Laravel
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
}
