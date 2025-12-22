<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt; // <-- Tambahkan ini
use Illuminate\Contracts\Encryption\DecryptException; // <-- Tambahkan ini

trait ManagesTenantConnection
{
    /**
     * Mengatur konfigurasi database 'tenant' secara dinamis.
     */
    protected function setTenantConnection(array $config): void
    {
        $hostToUse = empty($config['host']) ? 'force.fail.invalid' : $config['host'];
        $portToUse = empty($config['port']) ? '1' : $config['port'];
        $usernameToUse = empty($config['username']) ? 'invalid_user_fail' : $config['username'];

        // --- PROSES DEKRIPSI PASSWORD ---
        $passwordToUse = null;
        if (!empty($config['password'])) {
            try {
                // Mencoba mendekripsi password yang tersimpan di database
                $passwordToUse = Crypt::decryptString($config['password']);
            } catch (DecryptException $e) {
                // Jika gagal dekrip (mungkin password masih plain text atau key salah)
                // Kita gunakan nilai aslinya sebagai fallback
                $passwordToUse = $config['password'];

                // Opsional: Log peringatan jika password tidak terenkripsi
                // logger()->warning("Password tenant {$config['database']} tidak terenkripsi.");
            }
        }
        // ---------------------------------

        // 1. Mengubah konfigurasi koneksi 'tenant' secara dinamis
        Config::set('database.connections.tenant.database', $config['database']);
        Config::set('database.connections.tenant.host', $hostToUse);
        Config::set('database.connections.tenant.port', $portToUse);
        Config::set('database.connections.tenant.username', $usernameToUse);
        Config::set('database.connections.tenant.password', $passwordToUse);

        // 2. Bersihkan koneksi lama
        DB::purge('tenant');

        // 3. Set sebagai default
        DB::setDefaultConnection('tenant');
    }

    /**
     * Mengatur dan menguji koneksi database tenant.
     */
    protected function setAndTestTenantConnection(array $config): bool
    {
        try {
            $this->setTenantConnection($config);
            DB::connection('tenant')->getPdo();
            return true;
        } catch (\Exception $e) {
            logger()->error("Failed to connect to tenant database: {$config['database']}", [
                'error' => $e->getMessage()
            ]);

            // Kembalikan ke koneksi default utama (biasanya 'mysql' atau 'master')
            DB::setDefaultConnection(config('database.default'));
            return false;
        }
    }
}
