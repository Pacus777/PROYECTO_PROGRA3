<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Admin\EstadoPostulacionController;
use App\Http\Controllers\Web\Admin\EstudianteController;
use App\Http\Controllers\Web\Admin\EstudianteTutorVinculoController;
use App\Http\Controllers\Web\Admin\GestionController;
use App\Http\Controllers\Web\Admin\PostulacionNacionalController;
use App\Http\Controllers\Web\Admin\ReporteController;
use App\Http\Controllers\Web\Admin\TerritorioController;
use App\Http\Controllers\Web\Admin\TipoDocumentoController;
use App\Http\Controllers\Web\Admin\TutorVinculoController;
use App\Http\Controllers\Web\Admin\UnidadEducativaController;
use App\Http\Controllers\Web\Admin\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::patch('usuarios/{usuario}/activo', [UsuarioController::class, 'toggleActivo'])
    ->name('usuarios.toggle-activo');

Route::resource('usuarios', UsuarioController::class);

Route::resource('gestiones', GestionController::class)
    ->except(['show'])
    ->parameters(['gestiones' => 'gestion']);

Route::resource('unidades', UnidadEducativaController::class)
    ->parameters(['unidades' => 'unidad_educativa']);

Route::resource('estudiantes', EstudianteController::class)
    ->except(['show'])
    ->parameters(['estudiantes' => 'estudiante']);

Route::post('estudiantes/{estudiante}/tutores', [EstudianteTutorVinculoController::class, 'attach'])
    ->name('estudiantes.tutores.attach');
Route::delete('estudiantes/{estudiante}/tutores/{tutor}', [EstudianteTutorVinculoController::class, 'detach'])
    ->name('estudiantes.tutores.detach');

Route::get('tutores', [TutorVinculoController::class, 'listAll'])
    ->name('tutores.index');

Route::get('tutores/{tutor}/estudiantes', [TutorVinculoController::class, 'index'])
    ->name('tutores.estudiantes.index');
Route::post('tutores/{tutor}/estudiantes', [TutorVinculoController::class, 'attach'])
    ->name('tutores.estudiantes.attach');
Route::delete('tutores/{tutor}/estudiantes/{estudiante}', [TutorVinculoController::class, 'detach'])
    ->name('tutores.estudiantes.detach');

Route::prefix('territorio')->name('territorio.')->group(function (): void {
    Route::get('provincias', [TerritorioController::class, 'provincias'])->name('provincias');
    Route::get('municipios', [TerritorioController::class, 'municipios'])->name('municipios');
    Route::get('distritos', [TerritorioController::class, 'distritos'])->name('distritos');
    Route::get('unidades', [TerritorioController::class, 'unidades'])->name('unidades');
});

Route::get('postulaciones', [PostulacionNacionalController::class, 'index'])
    ->name('postulaciones.index');
Route::get('postulaciones/{postulacion}', [PostulacionNacionalController::class, 'show'])
    ->name('postulaciones.show');

Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
Route::get('reportes/export/postulaciones', [ReporteController::class, 'exportPostulaciones'])
    ->name('reportes.export.postulaciones');
Route::get('reportes/export/postulantes', [ReporteController::class, 'exportPostulantes'])
    ->name('reportes.export.postulantes');
Route::get('reportes/export/resumen-unidades', [ReporteController::class, 'exportResumenUnidades'])
    ->name('reportes.export.resumen-unidades');
Route::get('reportes/export/unidades', [ReporteController::class, 'exportUnidades'])
    ->name('reportes.export.unidades');
Route::get('reportes/export/tutores', [ReporteController::class, 'exportTutores'])
    ->name('reportes.export.tutores');
Route::get('reportes/export/usuarios', [ReporteController::class, 'exportUsuarios'])
    ->name('reportes.export.usuarios');

Route::get('postulaciones/export', [ReporteController::class, 'exportPostulaciones'])
    ->name('postulaciones.export');
Route::get('estudiantes/export', [ReporteController::class, 'exportPostulantes'])
    ->name('estudiantes.export');
Route::get('unidades/export', [ReporteController::class, 'exportUnidades'])
    ->name('unidades.export');
Route::get('unidades/export/resumen', [ReporteController::class, 'exportResumenUnidades'])
    ->name('unidades.export.resumen');
Route::get('tutores/export', [ReporteController::class, 'exportTutores'])
    ->name('tutores.export');
Route::get('usuarios/export', [ReporteController::class, 'exportUsuarios'])
    ->name('usuarios.export');

Route::resource('estados-postulacion', EstadoPostulacionController::class)
    ->except(['show'])
    ->parameters(['estados-postulacion' => 'estadoPostulacion']);

Route::resource('tipos-documento', TipoDocumentoController::class)
    ->except(['show'])
    ->parameters(['tipos-documento' => 'tipoDocumento']);
