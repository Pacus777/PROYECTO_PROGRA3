<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'notificacion';

    protected $primaryKey = 'id_not';

    protected $fillable = [
        'id_usu_not',
        'titulo_not',
        'mensaje_not',
        'leida_not',
        'enlace_not',
    ];

    protected function casts(): array
    {
        return [
            'leida_not' => 'boolean',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usu_not', 'id_usu');
    }
}
