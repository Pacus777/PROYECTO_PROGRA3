<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cupo extends Model
{
    protected $table = 'cupo';

    protected $primaryKey = 'id_cup';

    protected $fillable = [
        'id_oac_cup',
        'total_cup',
        'disponibles_cup',
    ];

    public function ofertaAcademica(): BelongsTo
    {
        return $this->belongsTo(OfertaAcademica::class, 'id_oac_cup', 'id_oac');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'id_cup_asi', 'id_cup');
    }

    public function getRouteKeyName(): string
    {
        return 'id_cup';
    }
}
