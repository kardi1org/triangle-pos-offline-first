<?php

namespace Modules\Shift\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Nama path untuk namespace controller di modul ini.
     */
    protected $moduleNamespace = 'Modules\Shift\Http\Controllers';

    /**
     * Definisikan rute untuk modul.
     */
    public function map()
    {
        $this->mapWebRoutes();
    }

    /**
     * Definisikan rute "web" untuk modul.
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Shift', '/Routes/web.php'));
    }
}
