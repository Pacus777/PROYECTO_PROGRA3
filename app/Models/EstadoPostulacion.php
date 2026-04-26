<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoPostulacion extends Model
{
    protected $table = 'estado_postulacion';

    protected $primaryKey = 'id_ept';

    protected $fillable = [
        'nombre_ept',
        'descripcion_ept',
    ];

    public function postulaciones(): HasMany
    {
        return $this->hasMany(Postulacion::class, 'id_ept_pos', 'id_ept');
    }
}
