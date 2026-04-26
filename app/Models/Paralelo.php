<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paralelo extends Model
{
    protected $table = 'paralelo';

    protected $primaryKey = 'id_par';

    protected $fillable = [
        'id_cur_par',
        'nombre_par',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_cur_par', 'id_cur');
    }

    public function ofertasAcademicas(): HasMany
    {
        return $this->hasMany(OfertaAcademica::class, 'id_par_oac', 'id_par');
    }

    public function getRouteKeyName(): string
    {
        return 'id_par';
    }
}
