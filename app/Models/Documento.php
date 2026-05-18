<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Documento extends Model
{
    protected $table = 'documento';

    protected $primaryKey = 'id_doc';

    protected $fillable = [
        'id_pos_doc',
        'id_tdo_doc',
        'ruta_archivo_doc',
        'estado_doc',
        'observacion_doc',
        'fecha_revision_doc',
    ];

    protected function casts(): array
    {
        return [
            'fecha_revision_doc' => 'datetime',
        ];
    }

    public function postulacion(): BelongsTo
    {
        return $this->belongsTo(Postulacion::class, 'id_pos_doc', 'id_pos');
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tdo_doc', 'id_tdo');
    }

    public function procesamientoOcr(): HasOne
    {
        return $this->hasOne(ProcesamientoOcr::class, 'id_doc_poc', 'id_doc');
    }

    public function estaAprobado(): bool
    {
        return $this->estado_doc === 'verificado';
    }

    public function estaPendiente(): bool
    {
        return $this->estado_doc === 'pendiente';
    }

    public function estaObservado(): bool
    {
        return $this->estado_doc === 'observado';
    }

    public function estaRechazado(): bool
    {
        return $this->estado_doc === 'rechazado';
    }
}
