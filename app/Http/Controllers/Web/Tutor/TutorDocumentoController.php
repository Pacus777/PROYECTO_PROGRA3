<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Tutor\Concerns\ResolvesTutorContext;
use App\Http\Requests\Web\Tutor\StoreDocumentoRequest;
use App\Models\Documento;
use App\Models\Postulacion;
use App\Models\TipoDocumento;
use App\Services\DocumentoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TutorDocumentoController extends Controller
{
    use ResolvesTutorContext;

    public function __construct(
        private readonly DocumentoService $service,
    ) {}

    public function index(Request $request): View
    {
        $estudianteIds = $this->tutorEstudianteIds($request);

        $documentos = Documento::query()
            ->whereHas('postulacion', fn ($q) => $q->whereIn('id_est_pos', $estudianteIds))
            ->with(['postulacion.estudiante.persona', 'tipoDocumento', 'procesamientoOcr'])
            ->orderByDesc('id_doc')
            ->paginate(20)
            ->withQueryString();

        return view('tutor.documentos.index', compact('documentos'));
    }

    public function create(Request $request, Postulacion $postulacion): View
    {
        $this->assertPostulacionBelongsToTutor($request, $postulacion);

        $tipos = TipoDocumento::query()->orderBy('nombre_tdo')->get();

        return view('tutor.documentos.create', compact('postulacion', 'tipos'));
    }

    public function store(StoreDocumentoRequest $request, Postulacion $postulacion): RedirectResponse
    {
        $this->assertPostulacionBelongsToTutor($request, $postulacion);

        $this->service->upload(
            $postulacion,
            (int) $request->validated()['id_tdo_doc'],
            $request->file('archivo'),
        );

        return redirect()
            ->route('tutor.postulaciones.show', $postulacion)
            ->with('success', 'Documento subido correctamente. Quedará en revisión.');
    }

    public function destroy(Request $request, Documento $documento): RedirectResponse
    {
        $estudianteIds = $this->tutorEstudianteIds($request);

        $documento->loadMissing('postulacion');

        abort_unless(
            in_array((int) $documento->postulacion->id_est_pos, $estudianteIds, true),
            403,
            'No tienes acceso a este documento.',
        );

        $postulacion = $documento->postulacion;

        $this->service->delete($documento);

        return redirect()
            ->route('tutor.postulaciones.show', $postulacion)
            ->with('success', 'Documento eliminado.');
    }

    public function download(Request $request, Documento $documento): BinaryFileResponse
    {
        $estudianteIds = $this->tutorEstudianteIds($request);

        $documento->loadMissing('postulacion');

        abort_unless(
            in_array((int) $documento->postulacion->id_est_pos, $estudianteIds, true),
            403,
        );

        $path = $this->service->diskPath($documento);

        abort_unless(file_exists($path), 404, 'Archivo no encontrado.');

        return response()->download($path);
    }
}
