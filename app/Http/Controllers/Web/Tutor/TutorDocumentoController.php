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
        abort_unless($postulacion->ofertaAcademica->estaAbiertaParaPostulacion(), 403, 'No se pueden subir documentos fuera del periodo de postulación.');

        $postulacion->loadMissing([
            'ofertaAcademica.tiposDocumentoRequeridos',
            'documentos',
        ]);

        $tiposYaCargados = $postulacion->documentos
            ->whereIn('estado_doc', ['pendiente', 'verificado'])
            ->pluck('id_tdo_doc')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $tipos = $postulacion->ofertaAcademica
            ? $postulacion->ofertaAcademica
                ->tiposDocumentoRequeridos()
                ->whereNotIn('tipo_documento.id_tdo', $tiposYaCargados)
                ->orderBy('nombre_tdo')
                ->get()
            : collect();

        return view('tutor.documentos.create', compact('postulacion', 'tipos'));
    }

    public function store(StoreDocumentoRequest $request, Postulacion $postulacion): RedirectResponse
    {
        $this->assertPostulacionBelongsToTutor($request, $postulacion);
        abort_unless($postulacion->ofertaAcademica->estaAbiertaParaPostulacion(), 403, 'No se pueden subir documentos fuera del periodo de postulación.');

        $this->service->upload(
            $postulacion,
            (int) $request->validated()['id_tdo_doc'],
            $request->file('archivo'),
        );

        return redirect()
            ->route('tutor.postulaciones.show', $postulacion)
            ->with('success', 'Documento subido correctamente. El colegio lo revisará en cuanto el sistema termine de leer el archivo.');
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
