<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Admin\EstudianteController;
use App\Http\Controllers\Web\Admin\GestionController;
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

Route::get('tutores', [TutorVinculoController::class, 'listAll'])
    ->name('tutores.index');

Route::get('tutores/{tutor}/estudiantes', [TutorVinculoController::class, 'index'])
    ->name('tutores.estudiantes.index');
Route::post('tutores/{tutor}/estudiantes', [TutorVinculoController::class, 'attach'])
    ->name('tutores.estudiantes.attach');
Route::delete('tutores/{tutor}/estudiantes/{estudiante}', [TutorVinculoController::class, 'detach'])
    ->name('tutores.estudiantes.detach');
