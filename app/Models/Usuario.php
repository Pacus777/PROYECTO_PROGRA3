<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    protected $table = 'usuario';

    protected $primaryKey = 'id_usu';

    protected $rememberTokenName = 'remember_token_usu';

    protected $fillable = [
        'id_rol_usu',
        'id_per_usu',
        'id_ued_usu',
        'correo_usu',
        'password_usu',
        'activo_usu',
    ];

    protected $hidden = [
        'password_usu',
        'remember_token_usu',
    ];

    protected function casts(): array
    {
        return [
            'password_usu' => 'hashed',
            'activo_usu' => 'boolean',
        ];
    }

    public function getAuthIdentifierName(): string
    {
        return 'id_usu';
    }

    public function getAuthPassword(): string
    {
        return $this->password_usu;
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'id_rol_usu', 'id_rol');
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_per_usu', 'id_per');
    }

    public function unidadEducativa(): BelongsTo
    {
        return $this->belongsTo(UnidadEducativa::class, 'id_ued_usu', 'id_ued');
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class, 'id_usu_not', 'id_usu');
    }

    public function getRouteKeyName(): string
    {
        return 'id_usu';
    }
}
