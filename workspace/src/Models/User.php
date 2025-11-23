<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email',
        'hash_password',
        'is_admin'
    ];
    
    // Ocultar password en JSON
    protected $hidden = ['hash_password'];
    
    // Timestamps automáticos
    public $timestamps = true;
}