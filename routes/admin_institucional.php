<?php

declare(strict_types=1);

use App\Http\Controllers\Web\AdminInstitucional\AcademicController;
use App\Http\Controllers\Web\AdminInstitucional\AsignacionController;
use App\Http\Controllers\Web\AdminInstitucional\CupoController;
use App\Http\Controllers\Web\AdminInstitucional\DashboardController;
use App\Http\Controllers\Web\AdminInstitucional\DocumentoController;
use App\Http\Controllers\Web\AdminInstitucional\EvaluacionController;
use App\Http\Controllers\Web\AdminInstitucional\HistorialController;
use App\Http\Controllers\Web\AdminInstitucional\ListaEsperaController;
use App\Http\Controllers\Web\AdminInstitucional\OfertaController;
use App\Http\Controllers\Web\AdminInstitucional\PostulacionController;
use App\Http\Controllers\Web\AdminInstitucional\ReporteController as InstitucionalReporteController;
use App\Http\Controllers\Web\AdminInstitucional\ResultadoController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::get('/academic', [AcademicController::class, 'index'])->name('academic.index');
Route::post('/niveles', [AcademicController::class, 'storeNivel'])->name('niveles.store');
Route::put('/niveles/{nivel}', [AcademicController::class, 'updateNivel'])->name('niveles.update');
Route::delete('/niveles/{nivel}', [AcademicController::class, 'destroyNivel'])->name('niveles.destroy');
Route::post('/cursos', [AcademicController::class, 'storeCurso'])->name('cursos.store');
Route::put('/cursos/{curso}', [AcademicController::class, 'updateCurso'])->name('cursos.update');
Route::delete('/cursos/{curso}', [AcademicController::class, 'destroyCurso'])->name('cursos.destroy');
Route::post('/paralelos', [AcademicController::class, 'storeParalelo'])->name('paralelos.store');
Route::put('/paralelos/{paralelo}', [AcademicController::class, 'updateParalelo'])->name('paralelos.update');
Route::delete('/paralelos/{paralelo}', [AcademicController::class, 'destroyParalelo'])->name('paralelos.destroy');

Route::resource('ofertas', OfertaController::class)
    ->except(['create', 'show'])
    ->parameters(['ofertas' => 'oferta_academica']);
Route::resource('cupos', CupoController::class)
    ->only(['store', 'update'])
    ->parameters(['cupos' => 'cupo'])
    ->names(['store' => 'cupos.store', 'update' => 'cupos.update']);

Route::get('/postulaciones', [PostulacionController::class, 'index'])->name('postulaciones.index');
Route::get('/postulaciones/export', [InstitucionalReporteController::class, 'exportPostulaciones'])->name('postulaciones.export');
Route::get('/postulaciones/{postulacion}', [PostulacionController::class, 'show'])->name('postulaciones.show');
Route::patch('/postulaciones/{postulacion}', [PostulacionController::class, 'update'])->name('postulaciones.update');

Route::get('/reportes', [InstitucionalReporteController::class, 'index'])->name('reportes.index');
Route::prefix('reportes/export')->name('reportes.export.')->group(function (): void {
    Route::get('/postulaciones', [InstitucionalReporteController::class, 'exportPostulaciones'])->name('postulaciones');
    Route::get('/ofertas', [InstitucionalReporteController::class, 'exportOfertas'])->name('ofertas');
    Route::get('/resultados', [InstitucionalReporteController::class, 'exportResultados'])->name('resultados');
    Route::get('/asignaciones', [InstitucionalReporteController::class, 'exportAsignaciones'])->name('asignaciones');
    Route::get('/lista-espera', [InstitucionalReporteController::class, 'exportListaEspera'])->name('lista-espera');
    Route::get('/historial', [InstitucionalReporteController::class, 'exportHistorial'])->name('historial');
    Route::get('/documentos', [InstitucionalReporteController::class, 'exportDocumentos'])->name('documentos');
    Route::get('/resumen-admision', [InstitucionalReporteController::class, 'exportResumenAdmision'])->name('resumen-admision');
});

Route::get('/ofertas/export', [InstitucionalReporteController::class, 'exportOfertas'])->name('ofertas.export');
Route::get('/resultados/export', [InstitucionalReporteController::class, 'exportResultados'])->name('resultados.export');
Route::get('/lista-espera/export', [InstitucionalReporteController::class, 'exportListaEspera'])->name('lista-espera.export');
Route::get('/historial/export', [InstitucionalReporteController::class, 'exportHistorial'])->name('historial.export');
Route::get('/documentos/export', [InstitucionalReporteController::class, 'exportDocumentos'])->name('documentos.export');

Route::resource('criterios', EvaluacionController::class)->only(['index', 'store', 'update', 'destroy']);
Route::post('/postulaciones/{postulacion}/evaluaciones', [EvaluacionController::class, 'storeEvaluacion'])->name('evaluaciones.store');
Route::put('/evaluaciones/{evaluacion}', [EvaluacionController::class, 'updateEvaluacion'])->name('evaluaciones.update');
Route::delete('/evaluaciones/{evaluacion}', [EvaluacionController::class, 'destroyEvaluacion'])->name('evaluaciones.destroy');

Route::get('/resultados', [ResultadoController::class, 'index'])->name('resultados.index');
Route::post('/resultados/sincronizar', [ResultadoController::class, 'sincronizar'])->name('resultados.sincronizar');
Route::get('/asignacion', [AsignacionController::class, 'index'])->name('asignacion.index');
Route::post('/asignacion/ejecutar', [AsignacionController::class, 'store'])->name('asignacion.store');

Route::get('/lista-espera', [ListaEsperaController::class, 'index'])->name('lista-espera.index');
Route::post('/lista-espera/{lista_espera}/asignar-cupo', [ListaEsperaController::class, 'asignarCupo'])->name('lista-espera.asignar-cupo');

Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');

Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
Route::patch('/documentos/{documento}/estado', [DocumentoController::class, 'updateEstado'])->name('documentos.estado');
Route::get('/documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
