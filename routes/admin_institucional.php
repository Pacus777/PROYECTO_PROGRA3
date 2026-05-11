<?php

declare(strict_types=1);

use App\Http\Controllers\Web\AdminInstitucional\AcademicController;
use App\Http\Controllers\Web\AdminInstitucional\AsignacionController;
use App\Http\Controllers\Web\AdminInstitucional\CupoController;
use App\Http\Controllers\Web\AdminInstitucional\DashboardController;
use App\Http\Controllers\Web\AdminInstitucional\DocumentoController;
use App\Http\Controllers\Web\AdminInstitucional\EvaluacionController;
use App\Http\Controllers\Web\AdminInstitucional\OfertaController;
use App\Http\Controllers\Web\AdminInstitucional\PostulacionController;
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
Route::get('/postulaciones/{postulacion}', [PostulacionController::class, 'show'])->name('postulaciones.show');

Route::resource('criterios', EvaluacionController::class)->only(['index', 'store', 'update', 'destroy']);
Route::post('/postulaciones/{postulacion}/evaluaciones', [EvaluacionController::class, 'storeEvaluacion'])->name('evaluaciones.store');
Route::put('/evaluaciones/{evaluacion}', [EvaluacionController::class, 'updateEvaluacion'])->name('evaluaciones.update');
Route::delete('/evaluaciones/{evaluacion}', [EvaluacionController::class, 'destroyEvaluacion'])->name('evaluaciones.destroy');

Route::get('/resultados', [ResultadoController::class, 'index'])->name('resultados.index');
Route::post('/asignacion/ejecutar', [AsignacionController::class, 'store'])->name('asignacion.store');

Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
Route::patch('/documentos/{documento}/estado', [DocumentoController::class, 'updateEstado'])->name('documentos.estado');
Route::get('/documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
