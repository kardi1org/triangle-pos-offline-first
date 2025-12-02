<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SetTenantDatabase
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan pengguna sudah login
        if (Auth::check()) {
            $tenantDatabaseName = Auth::user()->tenant_database;

            if ($tenantDatabaseName) {
                // Hati-hati: Kita mengubah koneksi 'mysql' default Laravel
                Config::set('database.connections.mysql.database', $tenantDatabaseName);

                // Penting: Purge dan Reconnect koneksi default
                // Ini memastikan semua query Eloquent dan DB Builder menggunakan DB Tenant
                DB::purge('mysql');
                DB::reconnect('mysql');
            }
        }

        return $next($request);
    }
}
