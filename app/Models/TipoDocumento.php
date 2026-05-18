<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoDocumento extends Model
{
    protected $table = 'tipo_documento';

    protected $primaryKey = 'id_tdo';

    protected $fillable = [
        'nombre_tdo',
    ];

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'id_tdo_doc', 'id_tdo');
    }

    public function ofertasAcademicas(): BelongsToMany
    {
        return $this->belongsToMany(
            OfertaAcademica::class,
            'oferta_documento_requerido',
            'id_tdo_odr',
            'id_oac_odr',
            'id_tdo',
            'id_oac'
        )->withPivot(['id_odr', 'obligatorio_odr'])
        ->withTimestamps();
    }
}