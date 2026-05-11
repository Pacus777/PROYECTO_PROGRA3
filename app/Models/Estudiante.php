<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    protected $table = 'estudiante';

    protected $primaryKey = 'id_est';

    protected $fillable = [
        'id_per_est',
        'codigo_est',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_est';
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_per_est', 'id_per');
    }

    public function tutores(): BelongsToMany
    {
        return $this->belongsToMany(Tutor::class, 'estudiante_tutor', 'id_est_ett', 'id_tut_ett')
            ->withTimestamps();
    }

    public function postulaciones(): HasMany
    {
        return $this->hasMany(Postulacion::class, 'id_est_pos', 'id_est');
    }

    public function detallesBoletin(): HasMany
    {
        return $this->hasMany(DetalleBoletin::class, 'id_est_dbo', 'id_est');
    }

    public function resumenesBoletin(): HasMany
    {
        return $this->hasMany(ResumenBoletin::class, 'id_est_rbo', 'id_est');
    }
}
