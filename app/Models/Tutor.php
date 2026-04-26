<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tutor extends Model
{
    protected $table = 'tutor';

    protected $primaryKey = 'id_tut';

    protected $fillable = [
        'id_per_tut',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_per_tut', 'id_per');
    }

    public function estudiantes(): BelongsToMany
    {
        return $this->belongsToMany(Estudiante::class, 'estudiante_tutor', 'id_tut_ett', 'id_est_ett')
            ->withTimestamps();
    }
}
