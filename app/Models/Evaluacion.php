<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluacion extends Model
{
    protected $table = 'evaluacion';

    protected $primaryKey = 'id_eva';

    protected $fillable = [
        'id_pos_eva',
        'id_cri_eva',
        'puntaje_eva',
        'observaciones_eva',
    ];

    public function postulacion(): BelongsTo
    {
        return $this->belongsTo(Postulacion::class, 'id_pos_eva', 'id_pos');
    }

    public function criterio(): BelongsTo
    {
        return $this->belongsTo(Criterio::class, 'id_cri_eva', 'id_cri');
    }

    public function getRouteKeyName(): string
    {
        return 'id_eva';
    }
}
