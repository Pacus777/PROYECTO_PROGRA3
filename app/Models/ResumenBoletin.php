<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumenBoletin extends Model
{
    protected $table = 'resumen_boletin';

    protected $primaryKey = 'id_rbo';

    protected $fillable = [
        'id_est_rbo',
        'id_ges_rbo',
        'promedio_rbo',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'id_est_rbo', 'id_est');
    }

    public function gestion(): BelongsTo
    {
        return $this->belongsTo(Gestion::class, 'id_ges_rbo', 'id_ges');
    }
}
