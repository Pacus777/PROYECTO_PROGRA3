<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UnidadEducativa;
use App\Models\Usuario;
use App\Support\Roles;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ColegioPublicoController extends Controller
{
    public function index(): View
    {
        $unidades = UnidadEducativa::query()
            ->with(['municipio.provincia.departamento'])
            ->withCount([
                'ofertasAcademicas as ofertas_abiertas_count' => fn ($q) => $q->abiertasParaPostulacion(),
            ])
            ->orderBy('nombre_ued')
            ->get();

        return view('public.colegios.index', [
            'unidades' => $unidades,
            'webUsuario' => $this->webUsuario(),
        ]);
    }

    public function show(Request $request, UnidadEducativa $unidad): View
    {
        $unidad->load([
            'municipio.provincia.departamento',
            'distritoEducativo',
            'ofertasAcademicas.gestion',
            'ofertasAcademicas.nivel',
            'ofertasAcademicas.curso',
            'ofertasAcademicas.paralelo',
            'ofertasAcademicas.tiposDocumentoRequeridos',
        ]);

        $ofertasAbiertas = $unidad->ofertasAcademicas
            ->filter(fn ($o) => $o->estaAbiertaParaPostulacion())
            ->values();

        $ofertasProximas = $unidad->ofertasAcademicas
            ->filter(fn ($o) => $o->estadoConvocatoria() === 'proxima')
            ->values();

        $ofertasCerradas = $unidad->ofertasAcademicas
            ->filter(fn ($o) => $o->estadoConvocatoria() === 'cerrada')
            ->values();

        if (! $this->esTutorAutenticado() && $ofertasAbiertas->isNotEmpty()) {
            $request->session()->put('postular_colegio', $unidad->codigo_ued);
            $request->session()->put('postular_colegio_nombre', $unidad->nombre_ued);
        }

        return view('public.colegios.show', [
            'unidad' => $unidad,
            'ofertasAbiertas' => $ofertasAbiertas,
            'ofertasProximas' => $ofertasProximas,
            'ofertasCerradas' => $ofertasCerradas,
            'webUsuario' => $this->webUsuario(),
            'esTutor' => $this->esTutorAutenticado(),
        ]);
    }

    private function webUsuario(): ?Usuario
    {
        $id = session('web_usuario_id');

        if ($id === null) {
            return null;
        }

        return Usuario::query()->with(['persona', 'rol'])->find($id);
    }

    private function esTutorAutenticado(): bool
    {
        $usuario = $this->webUsuario();

        return $usuario !== null && ($usuario->rol->nombre_rol ?? '') === Roles::TUTOR;
    }
}
