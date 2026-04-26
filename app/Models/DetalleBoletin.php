<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleBoletin extends Model
{
    protected $table = 'detalle_boletin';

    protected $primaryKey = 'id_dbo';

    protected $fillable = [
        'id_est_dbo',
        'id_ges_dbo',
        'materia_dbo',
        'nota_dbo',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'id_est_dbo', 'id_est');
    }

    public function gestion(): BelongsTo
    {
        return $this->belongsTo(Gestion::class, 'id_ges_dbo', 'id_ges');
    }
}
