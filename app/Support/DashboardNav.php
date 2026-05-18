<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Route;

final class DashboardNav
{
    /** @var array<string, array{label: string, index?: string}> */
    private const SECTIONS = [
        'usuarios' => ['label' => 'Cuentas de acceso', 'index' => 'admin.usuarios.index'],
        'unidades' => ['label' => 'Unidades educativas', 'index' => 'admin.unidades.index'],
        'estudiantes' => ['label' => 'Postulantes', 'index' => 'admin.estudiantes.index'],
        'tutores' => ['label' => 'Tutores', 'index' => 'admin.tutores.index'],
        'gestiones' => ['label' => 'Gestiones', 'index' => 'admin.gestiones.index'],
        'postulaciones' => ['label' => 'Postulaciones', 'index' => 'admin.postulaciones.index'],
        'reportes' => ['label' => 'Reportes', 'index' => 'admin.reportes.index'],
        'estados-postulacion' => ['label' => 'Estados postulación', 'index' => 'admin.estados-postulacion.index'],
        'tipos-documento' => ['label' => 'Tipos de documento', 'index' => 'admin.tipos-documento.index'],
        'academic' => ['label' => 'Académico', 'index' => 'admin.institucional.academic.index'],
        'ofertas' => ['label' => 'Ofertas', 'index' => 'admin.institucional.ofertas.index'],
        'documentos' => ['label' => 'Documentos', 'index' => 'admin.institucional.documentos.index'],
        'resultados' => ['label' => 'Resultados', 'index' => 'admin.institucional.resultados.index'],
        'criterios' => ['label' => 'Evaluación', 'index' => 'admin.institucional.criterios.index'],
        'seguimiento' => ['label' => 'Seguimiento', 'index' => 'tutor.seguimiento.index'],
        'perfil' => ['label' => 'Mi perfil', 'index' => 'tutor.perfil.index'],
    ];

    /** @var array<string, string> */
    private const ACTIONS = [
        'create' => 'Crear',
        'edit' => 'Editar',
        'show' => 'Detalle',
        'index' => '',
    ];

    public static function homeUrl(?string $role): string
    {
        return match ($role) {
            Roles::TUTOR => route('tutor.dashboard'),
            Roles::ADMIN_INSTITUCIONAL => route('admin.institucional.dashboard'),
            default => route('dashboard'),
        };
    }

    /**
     * @return list<array{label: string, url?: string}>
     */
    public static function breadcrumbs(?string $role): array
    {
        $homeUrl = self::homeUrl($role);
        $routeName = Route::currentRouteName();

        if ($routeName === null) {
            return [['label' => 'Inicio', 'url' => $homeUrl]];
        }

        if (in_array($routeName, ['dashboard', 'tutor.dashboard', 'admin.institucional.dashboard'], true)) {
            return [['label' => 'Inicio']];
        }

        $items = [['label' => 'Inicio', 'url' => $homeUrl]];
        $parts = explode('.', $routeName);

        if ($routeName === 'admin.tutores.estudiantes.index') {
            $items[] = ['label' => 'Tutores', 'url' => route('admin.tutores.index')];
            $items[] = ['label' => 'Estudiantes vinculados'];

            return $items;
        }

        $sectionKey = self::resolveSectionKey($parts, $role);
        if ($sectionKey === null) {
            $items[] = ['label' => self::humanize(end($parts) ?: 'Página')];

            return $items;
        }

        $section = self::SECTIONS[$sectionKey];
        $action = $parts[count($parts) - 1] ?? 'index';

        if ($action === 'index') {
            $items[] = ['label' => $section['label']];

            return $items;
        }

        if (isset($section['index']) && Route::has($section['index'])) {
            $items[] = ['label' => $section['label'], 'url' => route($section['index'])];
        } else {
            $items[] = ['label' => $section['label']];
        }

        $actionLabel = self::ACTIONS[$action] ?? self::humanize($action);
        if ($actionLabel !== '') {
            $items[] = ['label' => $actionLabel];
        }

        return $items;
    }

    /**
     * @return list<array{type: string, text: string, url?: string}>
     */
    public static function notifications(): array
    {
        $items = [];

        if ($message = session('success')) {
            $items[] = [
                'type' => 'success',
                'text' => (string) $message,
            ];
        }

        if ($errors = session('errors')) {
            $count = $errors->count();
            if ($count > 0) {
                $items[] = [
                    'type' => 'error',
                    'text' => $count === 1
                        ? 'Hay un error en el formulario.'
                        : "Hay {$count} errores en el formulario.",
                ];
            }
        }

        if ($items === []) {
            $items[] = [
                'type' => 'info',
                'text' => 'No tienes notificaciones nuevas.',
            ];
        }

        return $items;
    }

    /**
     * @return list<array{label: string, url: string, icon?: string}>
     */
    public static function settingsLinks(?string $role): array
    {
        return match ($role) {
            Roles::TUTOR => [
                ['label' => 'Mi perfil', 'url' => route('tutor.perfil.index')],
                ['label' => 'Mis postulaciones', 'url' => route('tutor.postulaciones.index')],
                ['label' => 'Seguimiento', 'url' => route('tutor.seguimiento.index')],
            ],
            Roles::ADMIN_INSTITUCIONAL => [
                ['label' => 'Panel institucional', 'url' => route('admin.institucional.dashboard')],
                ['label' => 'Académico', 'url' => route('admin.institucional.academic.index')],
                ['label' => 'Documentos / OCR', 'url' => route('admin.institucional.documentos.index')],
            ],
            Roles::ADMIN_GENERAL => [
                ['label' => 'Descargar reportes', 'url' => route('admin.reportes.index')],
                ['label' => 'Postulaciones', 'url' => route('admin.postulaciones.index')],
                ['label' => 'Gestiones', 'url' => route('admin.gestiones.index')],
            ],
            default => [
                ['label' => 'Inicio', 'url' => route('dashboard')],
            ],
        };
    }

    public static function profileUrl(?string $role): string
    {
        return match ($role) {
            Roles::TUTOR => route('tutor.perfil.index'),
            default => self::homeUrl($role),
        };
    }

    /**
     * @param  list<string>  $parts
     */
    private static function resolveSectionKey(array $parts, ?string $role): ?string
    {
        if (($parts[0] ?? '') === 'tutor' && isset($parts[1])) {
            return $parts[1];
        }

        if (($parts[0] ?? '') === 'admin') {
            if (($parts[1] ?? '') === 'institucional' && isset($parts[2])) {
                return $parts[2];
            }

            if (isset($parts[1]) && array_key_exists($parts[1], self::SECTIONS)) {
                return $parts[1];
            }
        }

        return null;
    }

    private static function humanize(string $value): string
    {
        return ucfirst(str_replace(['-', '_'], ' ', $value));
    }
}
