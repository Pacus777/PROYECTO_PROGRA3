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
        'prioridad_pos',
        'fecha_pos',
        'observaciones_pos',
        'aceptacion_cupo',
        'fecha_aceptacion_cupo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_pos' => 'datetime',
            'prioridad_pos' => 'integer',
            'aceptacion_cupo' => 'boolean',
            'fecha_aceptacion_cupo' => 'datetime',
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

    public function puedeAceptarCupo(): bool
    {
        $this->loadMissing('estadoPostulacion');

        return in_array($this->estadoPostulacion?->nombre_ept, ['admitido', 'admitida', 'aprobada'], true)
            && is_null($this->aceptacion_cupo);
    }

    public function totalDocumentosRequeridos(): int
{
    $this->loadMissing('ofertaAcademica.tiposDocumentoRequeridos');

    return $this->ofertaAcademica?->tiposDocumentoRequeridos?->count() ?? 0;
}

public function totalDocumentosValidos(): int
{
    $this->loadMissing('documentos');

    return $this->documentos
        ->whereIn('estado_doc', ['pendiente', 'verificado'])
        ->unique('id_tdo_doc')
        ->count();
}

public function documentosCompletos(): bool
{
    $totalRequeridos = $this->totalDocumentosRequeridos();

    if ($totalRequeridos === 0) {
        return false;
    }

    return $this->totalDocumentosValidos() >= $totalRequeridos;
}

public function porcentajeDocumental(): int
{
    $totalRequeridos = $this->totalDocumentosRequeridos();

    if ($totalRequeridos === 0) {
        return 0;
    }

    return (int) round(($this->totalDocumentosValidos() / $totalRequeridos) * 100);
}

public function etapaTutor(): string
{
    $this->loadMissing([
        'resultado',
        'asignaciones',
        'listasEspera',
        'documentos',
        'ofertaAcademica.tiposDocumentoRequeridos',
    ]);

    if ($this->asignaciones->isNotEmpty()) {
        return 'asignado';
    }

    if ($this->listasEspera->isNotEmpty()) {
        return 'lista_espera';
    }

    if ($this->resultado !== null) {
        return 'resultado';
    }

    if ($this->documentosCompletos()) {
        return 'documentos_completos';
    }

    if ($this->documentos->isNotEmpty()) {
        return 'documentos_revision';
    }

    return 'registrada';
}


public function documentosRequeridosVerificadosCompletos(): bool
{
    $this->loadMissing([
        'ofertaAcademica.tiposDocumentoRequeridos',
        'documentos',
    ]);

    $documentosRequeridos = $this->ofertaAcademica?->tiposDocumentoRequeridos ?? collect();

    if ($documentosRequeridos->isEmpty()) {
        return false;
    }

    $tiposVerificados = $this->documentos
        ->where('estado_doc', 'verificado')
        ->pluck('id_tdo_doc')
        ->map(static fn ($id): int => (int) $id)
        ->unique()
        ->values();

    $tiposRequeridos = $documentosRequeridos
        ->pluck('id_tdo')
        ->map(static fn ($id): int => (int) $id)
        ->unique()
        ->values();

    return $tiposRequeridos->diff($tiposVerificados)->isEmpty();
}

public function documentosFaltantesParaEvaluacion()
{
    $this->loadMissing([
        'ofertaAcademica.tiposDocumentoRequeridos',
        'documentos',
    ]);

    $documentosRequeridos = $this->ofertaAcademica?->tiposDocumentoRequeridos ?? collect();

    $tiposVerificados = $this->documentos
        ->where('estado_doc', 'verificado')
        ->pluck('id_tdo_doc')
        ->map(static fn ($id): int => (int) $id)
        ->unique()
        ->values();

    return $documentosRequeridos
        ->reject(fn ($tipo) => $tiposVerificados->contains((int) $tipo->id_tdo))
        ->values();
}

public function mensajeBloqueoEvaluacion(): string
{
    $this->loadMissing([
        'ofertaAcademica.tiposDocumentoRequeridos',
        'documentos.tipoDocumento',
    ]);

    $documentosRequeridos = $this->ofertaAcademica?->tiposDocumentoRequeridos ?? collect();

    if ($documentosRequeridos->isEmpty()) {
        return 'No se puede evaluar esta postulación porque la oferta académica no tiene documentos requeridos configurados.';
    }

    $faltantes = $this->documentosFaltantesParaEvaluacion();

    if ($faltantes->isEmpty()) {
        return '';
    }

    $nombres = $faltantes
        ->pluck('nombre_tdo')
        ->filter()
        ->implode(', ');

    return 'No se puede evaluar esta postulación. Faltan documentos verificados: '.$nombres.'.';
}


public function ultimaAsignacion()
{
    $this->loadMissing('asignaciones.cupo');

    return $this->asignaciones
        ->sortByDesc('id_asi')
        ->first();
}

public function asignacionActiva()
{
    $this->loadMissing('asignaciones.cupo');

    return $this->asignaciones
        ->whereIn('estado_asi', ['pendiente', 'asignado'])
        ->sortByDesc('id_asi')
        ->first();
}

public function cupoAceptado(): bool
{
    return $this->aceptacion_cupo === true;
}

public function cupoRechazado(): bool
{
    $ultima = $this->ultimaAsignacion();

    return $this->aceptacion_cupo === false
        && $ultima !== null
        && $ultima->estado_asi === 'rechazado';
}

public function cupoVencido(): bool
{
    $ultima = $this->ultimaAsignacion();

    return $this->aceptacion_cupo === false
        && $ultima !== null
        && $ultima->estado_asi === 'vencido';
}

public function puedeResponderCupo(): bool
{
    if ($this->aceptacion_cupo !== null) {
        return false;
    }

    $asignacion = $this->asignacionActiva();

    if ($asignacion === null) {
        return false;
    }

    if ($asignacion->fecha_limite_respuesta_asi !== null && now()->greaterThan($asignacion->fecha_limite_respuesta_asi)) {
        return false;
    }

    return true;
}


    public function getRouteKeyName(): string
    {
        return 'id_pos';
    }
}