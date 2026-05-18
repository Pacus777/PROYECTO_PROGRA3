<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OfertaAcademica extends Model
{
    protected $table = 'oferta_academica';

    protected $primaryKey = 'id_oac';

    protected $fillable = [
        'id_ges_oac',
        'id_ued_oac',
        'id_niv_oac',
        'id_cur_oac',
        'id_par_oac',
        'descripcion_oac',
        'fecha_inicio_postulacion_oac',
        'fecha_fin_postulacion_oac',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio_postulacion_oac' => 'datetime',
            'fecha_fin_postulacion_oac' => 'datetime',
        ];
    }

    public function gestion(): BelongsTo
    {
        return $this->belongsTo(Gestion::class, 'id_ges_oac', 'id_ges');
    }

    public function unidadEducativa(): BelongsTo
    {
        return $this->belongsTo(UnidadEducativa::class, 'id_ued_oac', 'id_ued');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'id_niv_oac', 'id_niv');
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_cur_oac', 'id_cur');
    }

    public function paralelo(): BelongsTo
    {
        return $this->belongsTo(Paralelo::class, 'id_par_oac', 'id_par');
    }

    public function cupos(): HasMany
    {
        return $this->hasMany(Cupo::class, 'id_oac_cup', 'id_oac');
    }

    public function postulaciones(): HasMany
    {
        return $this->hasMany(Postulacion::class, 'id_oac_pos', 'id_oac');
    }

    public function listasEspera(): HasMany
    {
        return $this->hasMany(ListaEspera::class, 'id_oac_les', 'id_oac');
    }

    public function getRouteKeyName(): string
    {
        return 'id_oac';
    }

    public function scopeAbiertasParaPostulacion(Builder $query): Builder
    {
        $ahora = now();

        return $query
            ->where('fecha_inicio_postulacion_oac', '<=', $ahora)
            ->where('fecha_fin_postulacion_oac', '>=', $ahora);
    }

    public function estaAbiertaParaPostulacion(): bool
    {
        return now()->betweenIncluded(
            $this->fecha_inicio_postulacion_oac,
            $this->fecha_fin_postulacion_oac
        );
    }

    public function estadoConvocatoria(): string
    {
        if (now()->lt($this->fecha_inicio_postulacion_oac)) {
            return 'proxima';
        }

        if ($this->estaAbiertaParaPostulacion()) {
            return 'abierta';
        }

        return 'cerrada';
    }

    public function tiposDocumentoRequeridos(): BelongsToMany
    {
        return $this->belongsToMany(
            TipoDocumento::class,
            'oferta_documento_requerido',
            'id_oac_odr',
            'id_tdo_odr',
            'id_oac',
            'id_tdo'
        )->withPivot(['id_odr', 'obligatorio_odr'])
        ->withTimestamps();
    }
}