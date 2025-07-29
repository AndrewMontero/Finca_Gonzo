<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * El path al que los usuarios son redirigidos después del login.
     */
    public const HOME = '/dashboard';

    /**
     * Define las rutas de la aplicación.
     */
    public function boot(): void
    {
        $this->routes(function () {
            // Rutas Web
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Rutas API
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }
}
