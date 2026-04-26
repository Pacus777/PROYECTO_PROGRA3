<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Persona extends Model
{
    protected $table = 'persona';

    protected $primaryKey = 'id_per';

    protected $fillable = [
        'ci_per',
        'nombres_per',
        'ap_paterno_per',
        'ap_materno_per',
        'fecha_nac_per',
        'genero_per',
        'correo_per',
        'telefono_per',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nac_per' => 'date',
        ];
    }

    public function usuario(): HasOne
    {
        return $this->hasOne(Usuario::class, 'id_per_usu', 'id_per');
    }

    public function tutor(): HasOne
    {
        return $this->hasOne(Tutor::class, 'id_per_tut', 'id_per');
    }

    public function estudiante(): HasOne
    {
        return $this->hasOne(Estudiante::class, 'id_per_est', 'id_per');
    }
}
