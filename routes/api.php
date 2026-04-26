<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EstudianteController;
use App\Http\Controllers\Api\PostulacionController;
use App\Http\Controllers\Api\UnidadEducativaController;
use App\Http\Controllers\Api\UsuarioController;
use App\Support\Roles;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::middleware('role:'.Roles::ADMIN_GENERAL)->group(function (): void {
            Route::apiResource('usuarios', UsuarioController::class);
        });

        Route::middleware('role:'.Roles::ADMIN_GENERAL.','.Roles::ADMIN_INSTITUCIONAL)->group(function (): void {
            Route::apiResource('unidad-educativas', UnidadEducativaController::class);
            Route::apiResource('estudiantes', EstudianteController::class);
        });

        Route::middleware('role:'.Roles::ADMIN_GENERAL.','.Roles::ADMIN_INSTITUCIONAL.','.Roles::TUTOR)->group(function (): void {
            Route::apiResource('postulaciones', PostulacionController::class)
                ->parameters(['postulaciones' => 'postulacion']);
        });
    });
});
