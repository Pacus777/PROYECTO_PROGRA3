<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asignacion;
use App\Models\Documento;
use App\Models\Evaluacion;
use App\Models\Historial;
use App\Models\ListaEspera;
use App\Models\Postulacion;
use App\Models\Resultado;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class HistorialInstitucionalService
{
    /** @var array<string, string> */
    private const MODULOS = [
        'postulacion' => 'Postulación',
        'evaluacion' => 'Evaluación',
        'documento' => 'Documento',
        'asignacion' => 'Asignación',
        'lista_espera' => 'Lista de espera',
        'resultado' => 'Resultado',
        'oferta_academica' => 'Oferta académica',
    ];

    /** @var array<string, string> */
    private const ACCIONES = [
        'crear' => 'Registro',
        'actualizar' => 'Actualización',
        'eliminar' => 'Eliminación',
        'cambio_estado' => 'Cambio de estado',
        'evaluacion' => 'Puntuación',
        'asignacion_cupo' => 'Cupo asignado',
        'asignacion_masiva' => 'Asignación masiva',
        'lista_espera' => 'En lista de espera',
        'cupo_desde_espera' => 'Cupo desde espera',
        'resultado_sync' => 'Ranking guardado',
        'documento_estado' => 'Estado de documento',
    ];

    public function registrar(?int $usuarioId, string $tabla, int $idRegistro, string $accion, array $datos = []): void
    {
        Historial::query()->create([
            'tabla_his' => $tabla,
            'id_registro_his' => $idRegistro,
            'accion_his' => $accion,
            'id_usu_his' => $usuarioId,
            'datos_his' => $datos !== [] ? $datos : null,
            'creado_his' => now(),
        ]);
    }

    /**
     * @return array{total: int, hoy: int, semana: int, por_modulo: Collection<int, object{modulo: string, label: string, total: int}>}
     */
    public function resumen(int $unidadId, Request $request): array
    {
        $timeline = $this->construirTimeline($unidadId, $request, sinPaginar: true);
        $hoy = now()->startOfDay();

        $porModulo = $timeline->groupBy('modulo')->map(function (Collection $grupo, string $modulo) {
            return (object) [
                'modulo' => $modulo,
                'label' => self::MODULOS[$modulo] ?? ucfirst(str_replace('_', ' ', $modulo)),
                'total' => $grupo->count(),
            ];
        })->sortByDesc('total')->values();

        return [
            'total' => $timeline->count(),
            'hoy' => $timeline->filter(fn ($e) => $e->fecha->gte($hoy))->count(),
            'semana' => $timeline->filter(fn ($e) => $e->fecha->gte(now()->subDays(7)))->count(),
            'por_modulo' => $porModulo->take(5),
        ];
    }

    public function paginarTimeline(int $unidadId, Request $request, int $perPage = 25): LengthAwarePaginator
    {
        $items = $this->construirTimeline($unidadId, $request, sinPaginar: true);
        $page = max(1, (int) $request->input('page', 1));
        $total = $items->count();
        $slice = $items->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );
    }

    /** @return Collection<int, object> */
    public function construirTimeline(int $unidadId, Request $request, bool $sinPaginar = false): Collection
    {
        $eventos = collect();

        foreach ($this->queryHistorialAuditoria($unidadId)->get() as $his) {
            $eventos->push($this->eventoDesdeHistorial($his));
        }

        foreach ($this->eventosSinteticos($unidadId) as $evento) {
            $eventos->push($evento);
        }

        $eventos = $eventos
            ->unique(fn ($e) => $e->clave)
            ->sortByDesc(fn ($e) => $e->fecha->timestamp)
            ->values();

        return $this->aplicarFiltros($eventos, $request);
    }

    private function queryHistorialAuditoria(int $unidadId): Builder
    {
        $postulacionIds = $this->idsPostulacionesUnidad($unidadId);
        $ofertaIds = $this->idsOfertasUnidad($unidadId);

        if ($postulacionIds->isEmpty()) {
            return Historial::query()->whereRaw('1 = 0');
        }

        return Historial::query()
            ->with('usuario.persona')
            ->where(function (Builder $q) use ($postulacionIds, $ofertaIds): void {
                $q->where(function (Builder $inner) use ($postulacionIds): void {
                    $inner->where('tabla_his', 'postulacion')
                        ->whereIn('id_registro_his', $postulacionIds);
                });

                if ($ofertaIds->isNotEmpty()) {
                    $q->orWhere(function (Builder $inner) use ($ofertaIds): void {
                        $inner->where('tabla_his', 'oferta_academica')
                            ->whereIn('id_registro_his', $ofertaIds);
                    });
                }

                $q->orWhere(function (Builder $inner) use ($postulacionIds): void {
                    $inner->where('tabla_his', 'asignacion')
                        ->whereIn('id_registro_his', function ($sub) use ($postulacionIds): void {
                            $sub->select('id_asi')
                                ->from('asignacion')
                                ->whereIn('id_pos_asi', $postulacionIds);
                        });
                });

                $q->orWhere(function (Builder $inner) use ($postulacionIds): void {
                    $inner->where('tabla_his', 'documento')
                        ->whereIn('id_registro_his', function ($sub) use ($postulacionIds): void {
                            $sub->select('id_doc')
                                ->from('documento')
                                ->whereIn('id_pos_doc', $postulacionIds);
                        });
                });

                $q->orWhere(function (Builder $inner) use ($postulacionIds): void {
                    $inner->where('tabla_his', 'evaluacion')
                        ->whereIn('id_registro_his', function ($sub) use ($postulacionIds): void {
                            $sub->select('id_eva')
                                ->from('evaluacion')
                                ->whereIn('id_pos_eva', $postulacionIds);
                        });
                });

                $q->orWhere(function (Builder $inner) use ($postulacionIds): void {
                    $inner->where('tabla_his', 'resultado')
                        ->whereIn('id_registro_his', function ($sub) use ($postulacionIds): void {
                            $sub->select('id_res')
                                ->from('resultado')
                                ->whereIn('id_pos_res', $postulacionIds);
                        });
                });

                $q->orWhere(function (Builder $inner) use ($ofertaIds): void {
                    $inner->where('tabla_his', 'lista_espera')
                        ->whereIn('id_registro_his', function ($sub) use ($ofertaIds): void {
                            $sub->select('id_les')
                                ->from('lista_espera')
                                ->whereIn('id_oac_les', $ofertaIds);
                        });
                });
            })
            ->orderByDesc('creado_his');
    }

    /** @return Collection<int, object> */
    private function eventosSinteticos(int $unidadId): Collection
    {
        $desde = now()->subMonths(6);
        $eventos = collect();

        Postulacion::query()
            ->with(['estudiante.persona', 'estadoPostulacion', 'ofertaAcademica.curso', 'ofertaAcademica.paralelo'])
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->where('created_at', '>=', $desde)
            ->orderByDesc('id_pos')
            ->limit(300)
            ->get()
            ->each(function (Postulacion $p) use ($eventos): void {
                $nombre = $this->nombreEstudiante($p);
                $oferta = $this->textoOferta($p->ofertaAcademica);
                $fecha = $p->fecha_pos ?? $p->created_at ?? now();

                $eventos->push($this->evento(
                    clave: "postulacion-{$p->id_pos}-crear",
                    fecha: Carbon::parse($fecha),
                    modulo: 'postulacion',
                    accion: 'crear',
                    descripcion: "Nueva postulación de {$nombre} a {$oferta}",
                    url: route('admin.institucional.postulaciones.show', $p),
                    usuario: null,
                ));

                if ($p->updated_at && $p->created_at && $p->updated_at->gt($p->created_at->addMinute())) {
                    $eventos->push($this->evento(
                        clave: "postulacion-{$p->id_pos}-upd-".$p->updated_at->timestamp,
                        fecha: $p->updated_at,
                        modulo: 'postulacion',
                        accion: 'actualizar',
                        descripcion: "Postulación de {$nombre} actualizada · estado: ".($p->estadoPostulacion->nombre_ept ?? '—'),
                        url: route('admin.institucional.postulaciones.show', $p),
                        usuario: null,
                    ));
                }
            });

        Asignacion::query()
            ->with(['postulacion.estudiante.persona', 'cupo.ofertaAcademica.curso', 'cupo.ofertaAcademica.paralelo'])
            ->whereHas('postulacion.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->where('estado_asi', 'asignado')
            ->where('fecha_asi', '>=', $desde)
            ->orderByDesc('fecha_asi')
            ->limit(200)
            ->get()
            ->each(function (Asignacion $a) use ($eventos): void {
                $p = $a->postulacion;
                $eventos->push($this->evento(
                    clave: "asignacion-{$a->id_asi}",
                    fecha: $a->fecha_asi ?? $a->created_at ?? now(),
                    modulo: 'asignacion',
                    accion: 'asignacion_cupo',
                    descripcion: 'Cupo asignado a '.$this->nombreEstudiante($p).' · '.$this->textoOferta($a->cupo?->ofertaAcademica),
                    url: $p ? route('admin.institucional.postulaciones.show', $p) : null,
                    usuario: null,
                ));
            });

        ListaEspera::query()
            ->with(['postulacion.estudiante.persona', 'ofertaAcademica.curso', 'ofertaAcademica.paralelo'])
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->where('created_at', '>=', $desde)
            ->orderByDesc('id_les')
            ->limit(200)
            ->get()
            ->each(function (ListaEspera $les) use ($eventos): void {
                $p = $les->postulacion;
                $eventos->push($this->evento(
                    clave: "lista-{$les->id_les}",
                    fecha: $les->created_at ?? now(),
                    modulo: 'lista_espera',
                    accion: 'lista_espera',
                    descripcion: $this->nombreEstudiante($p).' en lista de espera #'.$les->orden_les.' · '.$this->textoOferta($les->ofertaAcademica),
                    url: route('admin.institucional.lista-espera.index'),
                    usuario: null,
                ));
            });

        Documento::query()
            ->with(['postulacion.estudiante.persona', 'tipoDocumento'])
            ->whereHas('postulacion.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->where('updated_at', '>=', $desde)
            ->orderByDesc('updated_at')
            ->limit(200)
            ->get()
            ->each(function (Documento $doc) use ($eventos): void {
                $p = $doc->postulacion;
                $eventos->push($this->evento(
                    clave: "doc-{$doc->id_doc}-".$doc->updated_at?->timestamp,
                    fecha: $doc->updated_at ?? $doc->created_at ?? now(),
                    modulo: 'documento',
                    accion: 'documento_estado',
                    descripcion: 'Documento '.($doc->tipoDocumento->nombre_tdo ?? '—').' · '.($doc->estado_doc ?? '—').' · '.$this->nombreEstudiante($p),
                    url: route('admin.institucional.documentos.index'),
                    usuario: null,
                ));
            });

        Evaluacion::query()
            ->with(['postulacion.estudiante.persona', 'criterio'])
            ->whereHas('postulacion.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->where('created_at', '>=', $desde)
            ->orderByDesc('id_eva')
            ->limit(200)
            ->get()
            ->each(function (Evaluacion $eva) use ($eventos): void {
                $p = $eva->postulacion;
                $eventos->push($this->evento(
                    clave: "eva-{$eva->id_eva}",
                    fecha: $eva->created_at ?? now(),
                    modulo: 'evaluacion',
                    accion: 'evaluacion',
                    descripcion: 'Evaluación '.($eva->criterio->nombre_cri ?? '—').': '.number_format((float) $eva->puntaje_eva, 2).' · '.$this->nombreEstudiante($p),
                    url: $p ? route('admin.institucional.postulaciones.show', $p) : null,
                    usuario: null,
                ));
            });

        Resultado::query()
            ->with(['postulacion.estudiante.persona'])
            ->whereHas('postulacion.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->where('updated_at', '>=', $desde)
            ->orderByDesc('updated_at')
            ->limit(150)
            ->get()
            ->each(function (Resultado $res) use ($eventos): void {
                $p = $res->postulacion;
                $eventos->push($this->evento(
                    clave: "res-{$res->id_res}-".$res->updated_at?->timestamp,
                    fecha: $res->updated_at ?? $res->created_at ?? now(),
                    modulo: 'resultado',
                    accion: 'resultado_sync',
                    descripcion: 'Resultado: puntaje '.number_format((float) $res->puntaje_total_res, 2).' · orden #'.$res->clasificacion_res.' · '.$this->nombreEstudiante($p),
                    url: route('admin.institucional.resultados.index'),
                    usuario: null,
                ));
            });

        return $eventos;
    }

    private function eventoDesdeHistorial(Historial $his): object
    {
        $datos = $his->datos_his ?? [];
        $descripcion = (string) ($datos['descripcion'] ?? $this->descripcionPorDefecto($his));

        $url = null;
        if ($his->tabla_his === 'postulacion' && isset($datos['id_pos'])) {
            $url = route('admin.institucional.postulaciones.show', $datos['id_pos']);
        } elseif (isset($datos['url'])) {
            $url = (string) $datos['url'];
        }

        $usuario = $his->usuario;
        $nombreUsuario = $usuario ? $this->nombreUsuario($usuario) : ($datos['usuario'] ?? 'Sistema');

        return $this->evento(
            clave: 'his-'.$his->id_his,
            fecha: $his->creado_his ?? now(),
            modulo: $his->tabla_his,
            accion: $his->accion_his,
            descripcion: $descripcion,
            url: $url,
            usuario: $nombreUsuario,
            origen: 'auditoria',
        );
    }

    private function descripcionPorDefecto(Historial $his): string
    {
        $modulo = self::MODULOS[$his->tabla_his] ?? $his->tabla_his;
        $accion = self::ACCIONES[$his->accion_his] ?? $his->accion_his;

        return "{$accion} en {$modulo} (#{$his->id_registro_his})";
    }

    /**
     * @param  Collection<int, object>  $eventos
     * @return Collection<int, object>
     */
    private function aplicarFiltros(Collection $eventos, Request $request): Collection
    {
        if ($request->filled('modulo')) {
            $eventos = $eventos->where('modulo', $request->input('modulo'));
        }

        if ($request->filled('accion')) {
            $eventos = $eventos->where('accion', $request->input('accion'));
        }

        if ($request->filled('buscar')) {
            $term = mb_strtolower(trim((string) $request->input('buscar')));
            $eventos = $eventos->filter(function ($e) use ($term) {
                return str_contains(mb_strtolower($e->descripcion), $term)
                    || str_contains(mb_strtolower($e->usuario ?? ''), $term);
            });
        }

        if ($request->filled('desde')) {
            $desde = Carbon::parse($request->input('desde'))->startOfDay();
            $eventos = $eventos->filter(fn ($e) => $e->fecha->gte($desde));
        }

        if ($request->filled('hasta')) {
            $hasta = Carbon::parse($request->input('hasta'))->endOfDay();
            $eventos = $eventos->filter(fn ($e) => $e->fecha->lte($hasta));
        }

        return $eventos->values();
    }

    private function evento(
        string $clave,
        Carbon $fecha,
        string $modulo,
        string $accion,
        string $descripcion,
        ?string $url,
        ?string $usuario,
        string $origen = 'sistema',
    ): object {
        return (object) [
            'clave' => $clave,
            'fecha' => $fecha,
            'modulo' => $modulo,
            'modulo_label' => self::MODULOS[$modulo] ?? ucfirst(str_replace('_', ' ', $modulo)),
            'accion' => $accion,
            'accion_label' => self::ACCIONES[$accion] ?? ucfirst(str_replace('_', ' ', $accion)),
            'descripcion' => $descripcion,
            'url' => $url,
            'usuario' => $usuario,
            'origen' => $origen,
        ];
    }

    /** @return Collection<int, int> */
    private function idsPostulacionesUnidad(int $unidadId): Collection
    {
        return Postulacion::query()
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->pluck('id_pos');
    }

    /** @return Collection<int, int> */
    private function idsOfertasUnidad(int $unidadId): Collection
    {
        return DB::table('oferta_academica')
            ->where('id_ued_oac', $unidadId)
            ->pluck('id_oac');
    }

    private function nombreEstudiante(?Postulacion $postulacion): string
    {
        $per = $postulacion?->estudiante?->persona;

        return trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? '')) ?: 'Postulante';
    }

    private function textoOferta($oferta): string
    {
        if ($oferta === null) {
            return '—';
        }

        return trim(($oferta->curso->nombre_cur ?? '').' · '.($oferta->paralelo->nombre_par ?? '')) ?: 'Oferta';
    }

    private function nombreUsuario(Usuario $usuario): string
    {
        $per = $usuario->persona;

        return trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '')) ?: ($usuario->email_usu ?? 'Usuario');
    }

    /** @return array<string, string> */
    public function modulosParaFiltro(): array
    {
        return self::MODULOS;
    }

    /** @return array<string, string> */
    public function accionesParaFiltro(): array
    {
        return self::ACCIONES;
    }
}
