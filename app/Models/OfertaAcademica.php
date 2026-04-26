<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfertaAcademica extends Model
{
    protected $table = 'oferta_academica';

    protected $primaryKey = 'id_oac';

    protected $fillable = [
        'id_ges_oac',
        'id_ued_oac',
        'id_niv_oac',
        'id_cur_oac',
        'id_par_oac',
        'descripcion_oac',
    ];

    public function gestion(): BelongsTo
    {
        return $this->belongsTo(Gestion::class, 'id_ges_oac', 'id_ges');
    }

    public function unidadEducativa(): BelongsTo
    {
        return $this->belongsTo(UnidadEducativa::class, 'id_ued_oac', 'id_ued');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'id_niv_oac', 'id_niv');
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_cur_oac', 'id_cur');
    }

    public function paralelo(): BelongsTo
    {
        return $this->belongsTo(Paralelo::class, 'id_par_oac', 'id_par');
    }

    public function cupos(): HasMany
    {
        return $this->hasMany(Cupo::class, 'id_oac_cup', 'id_oac');
    }

    public function postulaciones(): HasMany
    {
        return $this->hasMany(Postulacion::class, 'id_oac_pos', 'id_oac');
    }

    public function listasEspera(): HasMany
    {
        return $this->hasMany(ListaEspera::class, 'id_oac_les', 'id_oac');
    }

    public function getRouteKeyName(): string
    {
        return 'id_oac';
    }
}
