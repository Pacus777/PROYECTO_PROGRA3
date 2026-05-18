<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\DistritoEducativo;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\UnidadEducativa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TerritorioController extends Controller
{
    public function provincias(Request $request): JsonResponse
    {
        $request->validate(['id_dep' => ['required', 'integer', 'exists:departamento,id_dep']]);

        $items = Provincia::query()
            ->where('id_dep_prov', (int) $request->input('id_dep'))
            ->orderBy('nombre_prov')
            ->get(['id_prov as id', 'nombre_prov as nombre']);

        return response()->json($items);
    }

    public function municipios(Request $request): JsonResponse
    {
        $request->validate(['id_prov' => ['required', 'integer', 'exists:provincia,id_prov']]);

        $items = Municipio::query()
            ->where('id_prov_mun', (int) $request->input('id_prov'))
            ->orderBy('nombre_mun')
            ->get(['id_mun as id', 'nombre_mun as nombre']);

        return response()->json($items);
    }

    public function distritos(Request $request): JsonResponse
    {
        $request->validate(['id_dep' => ['required', 'integer', 'exists:departamento,id_dep']]);

        $items = DistritoEducativo::query()
            ->where('id_dep_dis', (int) $request->input('id_dep'))
            ->orderBy('nombre_dis')
            ->get(['id_dis as id', 'nombre_dis as nombre', 'codigo_dis as codigo']);

        return response()->json($items);
    }

    public function unidades(Request $request): JsonResponse
    {
        $request->validate([
            'id_mun' => ['nullable', 'integer', 'exists:municipio,id_mun'],
            'id_dis' => ['nullable', 'integer', 'exists:distrito_educativo,id_dis'],
        ]);

        $query = UnidadEducativa::query()->orderBy('nombre_ued');

        if ($request->filled('id_mun')) {
            $query->where('id_mun_ued', (int) $request->input('id_mun'));
        }

        if ($request->filled('id_dis')) {
            $query->where('id_dis_ued', (int) $request->input('id_dis'));
        }

        $items = $query->get(['id_ued as id', 'nombre_ued as nombre', 'codigo_ued as codigo']);

        return response()->json($items);
    }
}
