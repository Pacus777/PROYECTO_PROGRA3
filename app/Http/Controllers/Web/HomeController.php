<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UnidadEducativa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        if ($request->filled('postular')) {
            $unidad = UnidadEducativa::query()
                ->where('codigo_ued', (string) $request->query('postular'))
                ->first();

            if ($unidad !== null) {
                return redirect()->route('colegios.show', $unidad);
            }
        }

        $colegiosDestacados = UnidadEducativa::query()
            ->with(['municipio.provincia.departamento'])
            ->withCount([
                'ofertasAcademicas as ofertas_abiertas_count' => fn ($q) => $q->abiertasParaPostulacion(),
            ])
            ->orderBy('nombre_ued')
            ->limit(6)
            ->get();

        return view('welcome', [
            'colegiosDestacados' => $colegiosDestacados,
        ]);
    }
}
