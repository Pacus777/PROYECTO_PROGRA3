<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\UnidadEducativa\StoreUnidadEducativaRequest;
use App\Http\Requests\Web\Admin\UnidadEducativa\UpdateUnidadEducativaRequest;
use App\Models\Departamento;
use App\Models\UnidadEducativa;
use App\Services\UnidadEducativaQueryService;
use App\Services\UnidadEducativaService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UnidadEducativaController extends Controller
{
    public function __construct(
        private readonly UnidadEducativaService $unidadService,
        private readonly UnidadEducativaQueryService $queryService,
    ) {}

    public function index(Request $request): View
    {
        $perPage = max(5, min(50, (int) $request->query('per_page', 15)));
        $unidades = $this->queryService
            ->queryFiltrada($request)
            ->withCount(['estudiantesMatriculados', 'ofertasAcademicas', 'usuarios'])
            ->paginate($perPage)
            ->withQueryString();

        $departamentos = Departamento::query()->orderBy('nombre_dep')->get();

        return view('admin.unidades.index', compact('unidades', 'departamentos'));
    }

    public function create(): View
    {
        return view('admin.unidades.create', [
            'departamentos' => Departamento::query()->orderBy('nombre_dep')->get(),
        ]);
    }

    public function store(StoreUnidadEducativaRequest $request): RedirectResponse
    {
        $this->unidadService->create($request->validated());

        return redirect()
            ->route('admin.unidades.index')
            ->with('success', 'Unidad educativa creada correctamente.');
    }

    public function show(UnidadEducativa $unidad_educativa): View
    {
        $unidad_educativa->loadCount(['usuarios', 'estudiantesMatriculados', 'ofertasAcademicas']);

        $matriculados = $unidad_educativa->estudiantesMatriculados()
            ->with('persona')
            ->orderByDesc('id_est')
            ->limit(100)
            ->get();

        return view('admin.unidades.show', [
            'unidad' => $unidad_educativa,
            'matriculados' => $matriculados,
        ]);
    }

    public function edit(UnidadEducativa $unidad_educativa): View
    {
        $unidad_educativa->load('municipio.provincia');

        return view('admin.unidades.edit', [
            'unidad' => $unidad_educativa,
            'departamentos' => Departamento::query()->orderBy('nombre_dep')->get(),
            'territorioSeleccionado' => [
                'id_dep' => $unidad_educativa->municipio?->provincia?->id_dep_prov,
                'id_prov' => $unidad_educativa->municipio?->id_prov_mun,
                'id_mun' => $unidad_educativa->id_mun_ued,
                'id_dis' => $unidad_educativa->id_dis_ued,
            ],
        ]);
    }

    public function update(UpdateUnidadEducativaRequest $request, UnidadEducativa $unidad_educativa): RedirectResponse
    {
        $this->unidadService->update($unidad_educativa, $request->validated());

        return redirect()
            ->route('admin.unidades.show', $unidad_educativa)
            ->with('success', 'Unidad educativa actualizada.');
    }

    public function destroy(UnidadEducativa $unidad_educativa): RedirectResponse
    {
        try {
            $this->unidadService->delete($unidad_educativa);
        } catch (QueryException) {
            return redirect()
                ->route('admin.unidades.index')
                ->with('error', 'No se puede eliminar la unidad: existen datos relacionados.');
        }

        return redirect()
            ->route('admin.unidades.index')
            ->with('success', 'Unidad educativa eliminada.');
    }
}
