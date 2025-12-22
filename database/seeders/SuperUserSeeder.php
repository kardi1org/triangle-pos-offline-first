<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Config; // <-- Tambahkan ini

class SuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // --- AMBIL KONFIGURASI KONEKSI DARI .ENV/config/database.php ---
        $dbConnection = Config::get('database.connections.mysql');

        $tenantDatabaseName = $dbConnection['database'];
        $tenantHost = $dbConnection['host'];
        $tenantPort = $dbConnection['port'];
        $tenantUsername = $dbConnection['username'];
        // --- AKHIR AMBIL KONFIGURASI ---

        $user = User::create([
            'name' => 'Administrator',
            'email' => 'super.admin@test.com',
            'password' => Hash::make(12345678),
            'is_active' => 1,

            // ✅ DATA DIISI SECARA OTOMATIS DARI KONFIGURASI AKTIF
            'tenant_database' => $tenantDatabaseName,
            'tenant_host' => $tenantHost,
            'tenant_port' => $tenantPort,
            'tenant_username' => $tenantUsername,

            // Pastikan format valid_date sudah benar
            'valid_date' => '2099-12-28'
        ]);

        $superAdmin = Role::create([
            'name' => 'Super Admin'
        ]);

        $user->assignRole($superAdmin);
    }
}
