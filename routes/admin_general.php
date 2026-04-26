<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Admin\UnidadEducativaController;
use App\Http\Controllers\Web\Admin\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::patch('usuarios/{usuario}/activo', [UsuarioController::class, 'toggleActivo'])
    ->name('usuarios.toggle-activo');

Route::resource('usuarios', UsuarioController::class);

Route::resource('unidades', UnidadEducativaController::class)
    ->parameters(['unidades' => 'unidad_educativa']);
