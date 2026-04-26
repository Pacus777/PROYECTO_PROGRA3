<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criterio extends Model
{
    protected $table = 'criterio';

    protected $primaryKey = 'id_cri';

    protected $fillable = [
        'id_tic_cri',
        'nombre_cri',
        'peso_cri',
    ];

    public function tipoCriterio(): BelongsTo
    {
        return $this->belongsTo(TipoCriterio::class, 'id_tic_cri', 'id_tic');
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'id_cri_eva', 'id_cri');
    }

    public function getRouteKeyName(): string
    {
        return 'id_cri';
    }
}
