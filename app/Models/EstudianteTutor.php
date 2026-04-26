<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstudianteTutor extends Model
{
    protected $table = 'estudiante_tutor';

    protected $primaryKey = 'id_ett';

    protected $fillable = [
        'id_est_ett',
        'id_tut_ett',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'id_est_ett', 'id_est');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class, 'id_tut_ett', 'id_tut');
    }
}
