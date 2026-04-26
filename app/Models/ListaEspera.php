<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListaEspera extends Model
{
    protected $table = 'lista_espera';

    protected $primaryKey = 'id_les';

    protected $fillable = [
        'id_pos_les',
        'id_oac_les',
        'orden_les',
    ];

    public function postulacion(): BelongsTo
    {
        return $this->belongsTo(Postulacion::class, 'id_pos_les', 'id_pos');
    }

    public function ofertaAcademica(): BelongsTo
    {
        return $this->belongsTo(OfertaAcademica::class, 'id_oac_les', 'id_oac');
    }
}
