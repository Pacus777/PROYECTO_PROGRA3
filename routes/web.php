<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\HomeController;
use App\Support\Roles;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('web.guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login.show');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('web.auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
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
