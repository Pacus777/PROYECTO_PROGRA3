<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    protected $table = 'rol';

    protected $primaryKey = 'id_rol';

    protected $fillable = [
        'nombre_rol',
        'descripcion_rol',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_rol_usu', 'id_rol');
    }
}
