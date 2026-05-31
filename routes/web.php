<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\TutorRegistroController;
use App\Http\Controllers\Web\ColegioPublicoController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\GeocodeController;
use App\Support\Roles;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/colegios', [ColegioPublicoController::class, 'index'])->name('colegios.index');
Route::get('/colegios/{unidad:codigo_ued}', [ColegioPublicoController::class, 'show'])->name('colegios.show');

Route::post('/asistente/chat', [TutorAssistantController::class, 'chat'])
    ->middleware('throttle:30,1')
    ->name('asistente.chat');

Route::middleware('web.guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login.show');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::post('/registro/tutor', [TutorRegistroController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('registro.tutor.store');
});

Route::middleware('web.auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware('throttle:60,1')->prefix('geocode')->name('geocode.')->group(function (): void {
        Route::get('/search', [GeocodeController::class, 'search'])->name('search');
        Route::get('/reverse', [GeocodeController::class, 'reverse'])->name('reverse');
    });
});

Route::middleware(['web.auth', 'web.role:'.Roles::ADMIN_GENERAL])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        require __DIR__.'/admin_general.php';
    });

Route::middleware(['web.auth', 'web.role:'.Roles::ADMIN_INSTITUCIONAL])
    ->prefix('admin/institucional')
    ->name('admin.institucional.')
    ->group(function (): void {
        require __DIR__.'/admin_institucional.php';
    });

require __DIR__.'/tutor_web.php';
