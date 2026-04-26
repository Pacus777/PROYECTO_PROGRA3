<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcesamientoOcr extends Model
{
    protected $table = 'procesamiento_ocr';

    protected $primaryKey = 'id_poc';

    protected $fillable = [
        'id_doc_poc',
        'texto_extraido_poc',
        'confianza_poc',
        'estado_poc',
    ];

    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'id_doc_poc', 'id_doc');
    }
}
