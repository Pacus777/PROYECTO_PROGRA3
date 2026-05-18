<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Tutor\TutorDashboardController;
use App\Http\Controllers\Web\Tutor\TutorDocumentoController;
use App\Http\Controllers\Web\Tutor\TutorEstudianteController;
use App\Http\Controllers\Web\Tutor\TutorPerfilController;
use App\Http\Controllers\Web\Tutor\TutorPostulacionController;
use App\Http\Controllers\Web\Tutor\TutorResultadoController;
use App\Http\Controllers\Web\Tutor\TutorSeguimientoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web.auth', 'web.role:tutor'])
    ->prefix('tutor')
    ->name('tutor.')
    ->group(function (): void {
        Route::get('/', TutorDashboardController::class)->name('dashboard');

        Route::get('/estudiantes', [TutorEstudianteController::class, 'index'])->name('estudiantes.index');
        Route::post('/estudiantes', [TutorEstudianteController::class, 'store'])->name('estudiantes.store');
        Route::delete('/estudiantes/{estudiante}', [TutorEstudianteController::class, 'destroy'])->name('estudiantes.destroy');

        Route::get('/postulaciones', [TutorPostulacionController::class, 'index'])->name('postulaciones.index');
        Route::get('/postulaciones/create', [TutorPostulacionController::class, 'create'])->name('postulaciones.create');
        Route::post('/postulaciones', [TutorPostulacionController::class, 'store'])->name('postulaciones.store');
        Route::get('/postulaciones/{postulacion}', [TutorPostulacionController::class, 'show'])->name('postulaciones.show');
        Route::post('/postulaciones/{postulacion}/responder-cupo', [TutorPostulacionController::class, 'responderCupo'])
            ->name('postulaciones.responder-cupo');

        Route::get('/documentos', [TutorDocumentoController::class, 'index'])->name('documentos.index');
        Route::get('/documentos/{documento}/download', [TutorDocumentoController::class, 'download'])->name('documentos.download');
        Route::delete('/documentos/{documento}', [TutorDocumentoController::class, 'destroy'])->name('documentos.destroy');
        Route::get('/postulaciones/{postulacion}/documentos/create', [TutorDocumentoController::class, 'create'])->name('documentos.create');
        Route::post('/postulaciones/{postulacion}/documentos', [TutorDocumentoController::class, 'store'])->name('documentos.store');

        Route::get('/seguimiento', [TutorSeguimientoController::class, 'index'])->name('seguimiento.index');

        Route::get('/resultados', [TutorResultadoController::class, 'index'])->name('resultados.index');

        Route::get('/perfil', [TutorPerfilController::class, 'index'])->name('perfil.index');
    });
