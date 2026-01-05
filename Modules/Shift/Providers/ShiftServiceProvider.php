<?php

namespace Modules\Shift\Providers;

use Illuminate\Support\ServiceProvider;

class ShiftServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'shift');
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
