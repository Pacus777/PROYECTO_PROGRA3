<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gestion extends Model
{
    protected $table = 'gestion';

    protected $primaryKey = 'id_ges';

    protected $fillable = [
        'nombre_ges',
        'fecha_ini_ges',
        'fecha_fin_ges',
        'activa_ges',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ini_ges' => 'date',
            'fecha_fin_ges' => 'date',
            'activa_ges' => 'boolean',
        ];
    }

    public function ofertasAcademicas(): HasMany
    {
        return $this->hasMany(OfertaAcademica::class, 'id_ges_oac', 'id_ges');
    }

    public function detallesBoletin(): HasMany
    {
        return $this->hasMany(DetalleBoletin::class, 'id_ges_dbo', 'id_ges');
    }

    public function resumenesBoletin(): HasMany
    {
        return $this->hasMany(ResumenBoletin::class, 'id_ges_rbo', 'id_ges');
    }
}
