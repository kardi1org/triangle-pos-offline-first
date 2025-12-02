<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ManagesTenantConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InitializeTenantConnection
{
    // Menggunakan Trait yang berisi setTenantConnection(array $config)
    use ManagesTenantConnection;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user sudah login
        if (auth()->check()) {
            $user = auth()->user();

            // Hanya proses jika user memiliki nama DB
            if ($user->tenant_database) {
                // =======================================================
                // FIX: Kumpulkan konfigurasi lengkap ke dalam ARRAY
                // Nilai yang kosong/NULL dari user->field akan diteruskan ke Trait.
                // =======================================================
                $tenantConfig = [
                    'database' => $user->tenant_database,
                    'host' => $user->tenant_host,
                    'port' => $user->tenant_port,
                    'username' => $user->tenant_username,
                    'password' => $user->tenant_password,
                ];

                // Panggil Trait dengan array konfigurasi lengkap
                $this->setTenantConnection($tenantConfig);
            }
        }

        return $next($request);
    }
}
