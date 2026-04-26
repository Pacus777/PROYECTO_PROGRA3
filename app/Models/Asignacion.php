<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asignacion extends Model
{
    protected $table = 'asignacion';

    protected $primaryKey = 'id_asi';

    protected $fillable = [
        'id_pos_asi',
        'id_cup_asi',
        'estado_asi',
        'fecha_asi',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asi' => 'datetime',
        ];
    }

    public function postulacion(): BelongsTo
    {
        return $this->belongsTo(Postulacion::class, 'id_pos_asi', 'id_pos');
    }

    public function cupo(): BelongsTo
    {
        return $this->belongsTo(Cupo::class, 'id_cup_asi', 'id_cup');
    }
}
