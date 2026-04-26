<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    protected $table = 'curso';

    protected $primaryKey = 'id_cur';

    protected $fillable = [
        'id_niv_cur',
        'nombre_cur',
    ];

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'id_niv_cur', 'id_niv');
    }

    public function paralelos(): HasMany
    {
        return $this->hasMany(Paralelo::class, 'id_cur_par', 'id_cur');
    }

    public function ofertasAcademicas(): HasMany
    {
        return $this->hasMany(OfertaAcademica::class, 'id_cur_oac', 'id_cur');
    }

    public function getRouteKeyName(): string
    {
        return 'id_cur';
    }
}
