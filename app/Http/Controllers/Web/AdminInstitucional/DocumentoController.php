<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Models\Documento;
use App\Services\DocumentoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentoController extends BaseInstitutionalController
{
    public function __construct(
        private readonly DocumentoService $service,
    ) {}

    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);

        $estado = $request->query('estado');

        $query = Documento::query()
            ->whereHas('postulacion.ofertaAcademica', fn ($q) => $q->where('id_ued_oac', $unidadId))
            ->with([
                'postulacion.estudiante.persona',
                'postulacion.ofertaAcademica.curso',
                'postulacion.ofertaAcademica.paralelo',
                'tipoDocumento',
                'procesamientoOcr',
            ])
            ->orderByDesc('id_doc');

        if (in_array($estado, ['pendiente', 'verificado', 'observado', 'rechazado'], true)) {
            $query->where('estado_doc', $estado);
        }

        $documentos = $query->paginate(20)->withQueryString();

        return view('admin.institucional.documentos.index', compact('documentos', 'estado'));
    }

    public function updateEstado(Request $request, Documento $documento): RedirectResponse
    {
        $validated = $request->validate([
            'estado_doc' => ['required', Rule::in(['pendiente', 'verificado', 'observado', 'rechazado'])],
            'observacion_doc' => ['nullable', 'string', 'max:2000', 'required_if:estado_doc,observado,rechazado'],
        ], [
            'observacion_doc.required_if' => 'Debe escribir una observación cuando el documento sea observado o rechazado.',
        ]);

        $this->assertDocumentoBelongsToUnidad($documento, $this->unidadId($request));

        $estadoAnterior = $documento->estado_doc;
        $this->service->updateEstado(
            $documento,
            $validated['estado_doc'],
            $validated['observacion_doc'] ?? null,
        );
        $documento->refresh()->load('tipoDocumento', 'postulacion.estudiante.persona');

        $this->registrarActividad($request, 'documento', (int) $documento->id_doc, 'documento_estado', [
            'descripcion' => 'Documento '.($documento->tipoDocumento->nombre_tdo ?? '—').': '.$estadoAnterior.' → '.$documento->estado_doc,
            'url' => route('admin.institucional.documentos.index'),
        ]);

        return back()->with('success', 'Estado actualizado.');
    }

    public function download(Request $request, Documento $documento): BinaryFileResponse
    {
        $this->assertDocumentoBelongsToUnidad($documento, $this->unidadId($request));

        $path = $this->service->diskPath($documento);

        abort_unless(file_exists($path), 404, 'Archivo no encontrado.');

        return response()->download($path);
    }

    private function assertDocumentoBelongsToUnidad(Documento $documento, int $unidadId): void
    {
        $documento->loadMissing('postulacion.ofertaAcademica');

        abort_unless(
            (int) $documento->postulacion?->ofertaAcademica?->id_ued_oac === $unidadId,
            403,
        );
    }
}
