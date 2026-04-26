<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoCriterio extends Model
{
    protected $table = 'tipo_criterio';

    protected $primaryKey = 'id_tic';

    protected $fillable = [
        'nombre_tic',
    ];

    public function criterios(): HasMany
    {
        return $this->hasMany(Criterio::class, 'id_tic_cri', 'id_tic');
    }
}
