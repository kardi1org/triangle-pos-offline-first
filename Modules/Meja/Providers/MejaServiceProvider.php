<?php

namespace Modules\Meja\Providers;

use Illuminate\Support\ServiceProvider;

class MejaServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Meja';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'meja';

    /**
     * Daftar Service Providers yang dimuat di modul ini.
     *
     * @var array
     */
    protected $providers = [
        // ✅ Wajib ada untuk memuat routes!
        \Modules\Meja\Providers\RouteServiceProvider::class,
        // Anda mungkin perlu menambahkan EventServiceProvider, dll., di sini.
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->loadViewsFrom(module_path($this->moduleName, 'Resources/views'), $this->moduleNameLower);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        //$this->app->register(EventServiceProvider::class); // Jika ada

        // Daftarkan semua service providers di dalam array $providers
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }
}
