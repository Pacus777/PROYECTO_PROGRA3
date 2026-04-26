<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Http\Controllers\Controller;
use App\Models\OfertaAcademica;
use App\Models\Postulacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class BaseInstitutionalController extends Controller
{
    protected function webUsuario(Request $request): Usuario
    {
        $usuarioId = (int) $request->session()->get('web_usuario_id');
        $usuario = Usuario::query()->with('rol')->find($usuarioId);

        if (! $usuario) {
            throw new HttpException(401, 'No autenticado.');
        }

        return $usuario;
    }

    protected function unidadId(Request $request): int
    {
        $unidadId = (int) ($this->webUsuario($request)->id_ued_usu ?? 0);
        if ($unidadId <= 0) {
            throw new HttpException(403, 'El usuario no tiene una unidad educativa asignada.');
        }

        return $unidadId;
    }

    protected function assertOfertaBelongsToUnidad(OfertaAcademica $oferta, int $unidadId): void
    {
        if ((int) $oferta->id_ued_oac !== $unidadId) {
            throw new HttpException(403, 'Oferta académica fuera de tu unidad educativa.');
        }
    }

    protected function assertPostulacionBelongsToUnidad(Postulacion $postulacion, int $unidadId): void
    {
        $ofertaUnidadId = (int) optional($postulacion->ofertaAcademica)->id_ued_oac;
        if ($ofertaUnidadId !== $unidadId) {
            throw new HttpException(403, 'Postulación fuera de tu unidad educativa.');
        }
    }
}

