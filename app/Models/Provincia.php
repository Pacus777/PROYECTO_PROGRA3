<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provincia extends Model
{
    protected $table = 'provincia';

    protected $primaryKey = 'id_prov';

    protected $fillable = [
        'id_dep_prov',
        'nombre_prov',
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'id_dep_prov', 'id_dep');
    }

    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class, 'id_prov_mun', 'id_prov');
    }
}
