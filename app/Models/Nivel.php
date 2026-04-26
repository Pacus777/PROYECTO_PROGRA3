<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nivel extends Model
{
    protected $table = 'nivel';

    protected $primaryKey = 'id_niv';

    protected $fillable = [
        'nombre_niv',
    ];

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'id_niv_cur', 'id_niv');
    }

    public function ofertasAcademicas(): HasMany
    {
        return $this->hasMany(OfertaAcademica::class, 'id_niv_oac', 'id_niv');
    }

    public function getRouteKeyName(): string
    {
        return 'id_niv';
    }
}
