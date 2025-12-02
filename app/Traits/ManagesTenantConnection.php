<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

trait ManagesTenantConnection
{
    /**
     * Mengatur konfigurasi database 'tenant' secara dinamis dan
     * menyetelnya sebagai koneksi default.
     *
     * Jika field host kosong, akan diisi dengan host dummy yang pasti gagal
     * untuk mencegah fallback ke localhost.
     *
     * @param array $config Konfigurasi koneksi lengkap (database, host, port, username, password).
     * @return void
     */
    protected function setTenantConnection(array $config): void
    {
        // Tentukan Host yang akan digunakan
        // Jika host di DB kosong, gunakan nama host yang pasti tidak ada
        // untuk memastikan koneksi gagal dan tidak fallback ke localhost.
        // untuk memastikan koneksi gagal dan tidak fallback ke localhost.
        $hostToUse = empty($config['host']) ? 'force.fail.invalid' : $config['host'];

        // Jika port kosong, gunakan port 1 yang reserved (pasti gagal)
        $portToUse = empty($config['port']) ? '1' : $config['port'];

        // Jika username kosong, gunakan nama user yang tidak mungkin ada
        $usernameToUse = empty($config['username']) ? 'invalid_user_fail' : $config['username'];

        // Jika password kosong, gunakan string acak yang pasti salah
        $passwordToUse = empty($config['password']) ? null : $config['password'];
        // ==========================================================

        // 1. Mengubah SEMUA konfigurasi koneksi 'tenant' secara dinamis
        Config::set('database.connections.tenant.database', $config['database']);
        Config::set('database.connections.tenant.host', $hostToUse);
        Config::set('database.connections.tenant.port', $portToUse);
        Config::set('database.connections.tenant.username', $usernameToUse);
        Config::set('database.connections.tenant.password', $passwordToUse);

        // 2. Membersihkan (purge) koneksi 'tenant' yang lama agar menggunakan konfigurasi baru
        DB::purge('tenant');

        // 3. Menyeting koneksi 'tenant' sebagai koneksi default.
        DB::setDefaultConnection('tenant');
    }

    /**
     * Mengatur dan menguji koneksi database tenant (dipakai di LoginController).
     *
     * @param array $config Konfigurasi koneksi lengkap.
     * @return bool
     */
    protected function setAndTestTenantConnection(array $config): bool
    {
        try {
            $this->setTenantConnection($config);
            // Mencoba mendapatkan PDO untuk menguji koneksi secara fisik
            DB::connection('tenant')->getPdo();
            return true;
        } catch (\Exception $e) {
            // Log error
            logger()->error("Failed to connect to tenant database: {$config['database']}", ['error' => $e->getMessage(), 'config' => $config]);

            // Set kembali koneksi ke default (master) agar error tidak mengganggu app master
            DB::setDefaultConnection(config('database.default'));
            return false;
        }
    }
}
