<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historial extends Model
{
    protected $table = 'historial';

    protected $primaryKey = 'id_his';

    public $timestamps = false;

    const CREATED_AT = 'creado_his';

    const UPDATED_AT = null;

    protected $fillable = [
        'tabla_his',
        'id_registro_his',
        'accion_his',
        'id_usu_his',
        'datos_his',
    ];

    protected function casts(): array
    {
        return [
            'datos_his' => 'array',
            'creado_his' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usu_his', 'id_usu');
    }
}
