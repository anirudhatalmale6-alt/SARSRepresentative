<?php

namespace Modules\SARSRepresentative\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $moduleNamespace = 'Modules\\SARSRepresentative\\Http\\Controllers';

    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix('cims/sarsrep')
            ->name('sarsrep.')
            ->namespace($this->moduleNamespace)
            ->group(module_path('SARSRepresentative', '/Routes/web.php'));
    }
}
