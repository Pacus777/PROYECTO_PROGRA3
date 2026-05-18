<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipio extends Model
{
    protected $table = 'municipio';

    protected $primaryKey = 'id_mun';

    protected $fillable = [
        'id_prov_mun',
        'nombre_mun',
    ];

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class, 'id_prov_mun', 'id_prov');
    }

    public function unidadesEducativas(): HasMany
    {
        return $this->hasMany(UnidadEducativa::class, 'id_mun_ued', 'id_mun');
    }
}
