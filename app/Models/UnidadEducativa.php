<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadEducativa extends Model
{
    protected $table = 'unidad_educativa';

    protected $primaryKey = 'id_ued';

    protected $fillable = [
        'nombre_ued',
        'codigo_ued',
        'direccion_ued',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_ued_usu', 'id_ued');
    }

    public function ofertasAcademicas(): HasMany
    {
        return $this->hasMany(OfertaAcademica::class, 'id_ued_oac', 'id_ued');
    }

    public function getRouteKeyName(): string
    {
        return 'id_ued';
    }
}
