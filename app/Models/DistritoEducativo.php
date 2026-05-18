<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DistritoEducativo extends Model
{
    protected $table = 'distrito_educativo';

    protected $primaryKey = 'id_dis';

    protected $fillable = [
        'id_dep_dis',
        'codigo_dis',
        'nombre_dis',
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'id_dep_dis', 'id_dep');
    }

    public function unidadesEducativas(): HasMany
    {
        return $this->hasMany(UnidadEducativa::class, 'id_dis_ued', 'id_dis');
    }
}
