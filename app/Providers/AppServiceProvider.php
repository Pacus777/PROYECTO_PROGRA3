<?php

namespace App\Providers;

use App\Models\Usuario;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view): void {
            $id = session('web_usuario_id');
            $view->with(
                'layoutWebUsuario',
                $id ? Usuario::query()->with(['persona', 'rol'])->find($id) : null,
            );
        });
    }
}
