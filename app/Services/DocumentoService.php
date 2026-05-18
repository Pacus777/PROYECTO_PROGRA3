<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Documento;
use App\Models\ProcesamientoOcr;
use App\Models\Postulacion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentoService
{
    public function upload(Postulacion $postulacion, int $tipoId, UploadedFile $archivo): Documento
    {
        return DB::transaction(function () use ($postulacion, $tipoId, $archivo): Documento {
            $path = $archivo->store('documentos/'.$postulacion->id_pos, 'local');

            $documento = Documento::query()->create([
                'id_pos_doc' => $postulacion->id_pos,
                'id_tdo_doc' => $tipoId,
                'ruta_archivo_doc' => $path,
                'estado_doc' => 'pendiente',
                'observacion_doc' => null,
                'fecha_revision_doc' => null,
            ]);

            ProcesamientoOcr::query()->create([
                'id_doc_poc' => $documento->id_doc,
                'estado_poc' => 'pendiente',
            ]);

            return $documento;
        });
    }

    public function updateEstado(Documento $documento, string $estado, ?string $observacion = null): void
    {
        $documento->update([
            'estado_doc' => $estado,
            'observacion_doc' => $observacion,
            'fecha_revision_doc' => now(),
        ]);
    }

    public function delete(Documento $documento): void
    {
        DB::transaction(function () use ($documento): void {
            Storage::disk('local')->delete($documento->ruta_archivo_doc);
            $documento->delete();
        });
    }

    public function diskPath(Documento $documento): string
    {
        return Storage::disk('local')->path($documento->ruta_archivo_doc);
    }
}
