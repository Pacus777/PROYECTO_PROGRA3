<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resultado extends Model
{
    protected $table = 'resultado';

    protected $primaryKey = 'id_res';

    protected $fillable = [
        'id_pos_res',
        'puntaje_total_res',
        'clasificacion_res',
    ];

    public function postulacion(): BelongsTo
    {
        return $this->belongsTo(Postulacion::class, 'id_pos_res', 'id_pos');
    }
}
