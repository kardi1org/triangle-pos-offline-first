<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Gate::before(function ($user, $ability) {
        //     return $user->hasRole('Super Admin') ? true : null;
        // });

        Gate::before(function ($user, $ability) {

            // ✅ Super Admin tetap full akses
            if ($user->hasRole('Super Admin')) {
                return true;
            }

            // ✅ SEMUA USER LOGIN boleh Inventory
            if (str_contains($ability, 'inventories')) {
                return true;
            }

            return null;
        });
    }
}
