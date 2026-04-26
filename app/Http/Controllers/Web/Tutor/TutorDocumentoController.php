<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Tutor\Concerns\ResolvesTutorContext;
use App\Models\Documento;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TutorDocumentoController extends Controller
{
    use ResolvesTutorContext;

    public function index(Request $request): View
    {
        $estudianteIds = $this->tutorEstudianteIds($request);

        $documentos = Documento::query()
            ->whereHas('postulacion', function ($q) use ($estudianteIds): void {
                $q->whereIn('id_est_pos', $estudianteIds);
            })
            ->with(['postulacion.estudiante.persona', 'tipoDocumento', 'procesamientoOcr'])
            ->orderByDesc('id_doc')
            ->paginate(20)
            ->withQueryString();

        return view('tutor.documentos.index', compact('documentos'));
    }
}
