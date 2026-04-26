<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Tutor\Concerns;

use App\Models\Postulacion;
use App\Models\Tutor;
use App\Models\Usuario;
use Illuminate\Http\Request;

trait ResolvesTutorContext
{
    protected function webUsuario(Request $request): ?Usuario
    {
        $usuario = $request->attributes->get('web_usuario');
        if ($usuario instanceof Usuario) {
            return $usuario;
        }

        $usuarioId = $request->session()->get('web_usuario_id');

        return $usuarioId !== null
            ? Usuario::query()->with(['persona', 'rol'])->find($usuarioId)
            : null;
    }

    protected function tutorFromRequest(Request $request): ?Tutor
    {
        $usuario = $this->webUsuario($request);
        if ($usuario === null || $usuario->id_per_usu === null) {
            return null;
        }

        return Tutor::query()
            ->where('id_per_tut', $usuario->id_per_usu)
            ->with(['estudiantes.persona'])
            ->first();
    }

    /**
     * @return list<int>
     */
    protected function tutorEstudianteIds(Request $request): array
    {
        $tutor = $this->tutorFromRequest($request);
        if ($tutor === null) {
            return [];
        }

        return $tutor->estudiantes->pluck('id_est')->map(static fn ($id): int => (int) $id)->all();
    }

    protected function assertPostulacionBelongsToTutor(Request $request, Postulacion $postulacion): void
    {
        abort_unless(
            in_array((int) $postulacion->id_est_pos, $this->tutorEstudianteIds($request), true),
            403,
            'No tienes acceso a esta postulación.',
        );
    }
}
