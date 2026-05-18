<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    protected $table = 'departamento';

    protected $primaryKey = 'id_dep';

    protected $fillable = [
        'codigo_dep',
        'nombre_dep',
    ];

    public function provincias(): HasMany
    {
        return $this->hasMany(Provincia::class, 'id_dep_prov', 'id_dep');
    }

    public function distritosEducativos(): HasMany
    {
        return $this->hasMany(DistritoEducativo::class, 'id_dep_dis', 'id_dep');
    }
}
