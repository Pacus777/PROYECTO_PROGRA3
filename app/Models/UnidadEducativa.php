<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadEducativa extends Model
{
    protected $table = 'unidad_educativa';

    protected $primaryKey = 'id_ued';

    protected $fillable = [
        'nombre_ued',
        'codigo_ued',
        'direccion_ued',
        'descripcion_ued',
        'telefono_ued',
        'correo_ued',
        'turno_ued',
        'niveles_ued',
        'imagen_portada_ued',
        'galeria_ued',
        'lat_ued',
        'lng_ued',
        'id_mun_ued',
        'id_dis_ued',
    ];

    protected function casts(): array
    {
        return [
            'lat_ued' => 'float',
            'lng_ued' => 'float',
            'galeria_ued' => 'array',
        ];
    }

    /** @return list<string> */
    public function fotosGaleria(): array
    {
        $fotos = is_array($this->galeria_ued) ? $this->galeria_ued : [];

        return array_values(array_filter($fotos, fn ($url) => is_string($url) && $url !== ''));
    }

    public function urlPortada(): ?string
    {
        $portada = $this->imagen_portada_ued;
        if (is_string($portada) && $portada !== '') {
            return $portada;
        }

        $galeria = $this->fotosGaleria();

        return $galeria[0] ?? null;
    }

    public function tienePerfilPublico(): bool
    {
        return ($this->descripcion_ued ?? '') !== ''
            || $this->urlPortada() !== null
            || $this->fotosGaleria() !== []
            || ($this->telefono_ued ?? '') !== ''
            || ($this->correo_ued ?? '') !== '';
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'id_mun_ued', 'id_mun');
    }

    public function distritoEducativo(): BelongsTo
    {
        return $this->belongsTo(DistritoEducativo::class, 'id_dis_ued', 'id_dis');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'id_ued_usu', 'id_ued');
    }

    public function ofertasAcademicas(): HasMany
    {
        return $this->hasMany(OfertaAcademica::class, 'id_ued_oac', 'id_ued');
    }

    public function estudiantesMatriculados(): HasMany
    {
        return $this->hasMany(Estudiante::class, 'id_ued_mat_est', 'id_ued');
    }

    public function getRouteKeyName(): string
    {
        return 'id_ued';
    }
}
