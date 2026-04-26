<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Postulacion extends Model
{
    protected $table = 'postulacion';

    protected $primaryKey = 'id_pos';

    protected $fillable = [
        'id_est_pos',
        'id_oac_pos',
        'id_ept_pos',
        'fecha_pos',
        'observaciones_pos',
    ];

    protected function casts(): array
    {
        return [
            'fecha_pos' => 'datetime',
        ];
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'id_est_pos', 'id_est');
    }

    public function ofertaAcademica(): BelongsTo
    {
        return $this->belongsTo(OfertaAcademica::class, 'id_oac_pos', 'id_oac');
    }

    public function estadoPostulacion(): BelongsTo
    {
        return $this->belongsTo(EstadoPostulacion::class, 'id_ept_pos', 'id_ept');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'id_pos_doc', 'id_pos');
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'id_pos_eva', 'id_pos');
    }

    public function resultado(): HasOne
    {
        return $this->hasOne(Resultado::class, 'id_pos_res', 'id_pos');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'id_pos_asi', 'id_pos');
    }

    public function listasEspera(): HasMany
    {
        return $this->hasMany(ListaEspera::class, 'id_pos_les', 'id_pos');
    }

    public function getRouteKeyName(): string
    {
        return 'id_pos';
    }
}
