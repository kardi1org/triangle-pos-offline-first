<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Category;
use Modules\Setting\Entities\Unit;
use App\Models\User; // <-- Pastikan namespace User Anda benar
use Illuminate\Support\Facades\DB; // <-- Tambahkan
use Illuminate\Support\Facades\Config; // <-- Tambahkan

class ProductDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // 1. Ambil data user/tenant yang sudah disave di tabel master
        // Asumsi Super Admin adalah user pertama.
        $user = User::where('email', 'super.admin@test.com')->first();

        if ($user && $user->tenant_database) {

            $dbName = $user->tenant_database;

            // 2. Setel konfigurasi koneksi 'tenant'
            Config::set('database.connections.tenant.database', $dbName);
            Config::set('database.connections.tenant.host', $user->tenant_host);
            Config::set('database.connections.tenant.port', $user->tenant_port);
            Config::set('database.connections.tenant.username', $user->tenant_username);
            // Ambil password dari .env karena tidak disimpan di tabel user
            Config::set('database.connections.tenant.password', env('DB_PASSWORD'));

            // 3. Purge dan Reconnect koneksi 'tenant'
            DB::purge('tenant');
            DB::reconnect('tenant');

            // 4. Jalankan Seeder Tenant Data menggunakan koneksi 'tenant'

            // Jika Category & Unit menggunakan koneksi 'tenant' secara default di Model:
            Category::create([
                'category_code' => 'CA_01',
                'category_name' => 'Random'
            ]);

            Unit::create([
                'name' => 'Piece',
                'short_name' => 'PC',
                'operator' => '*',
                'operation_value' => 1
            ]);

            // Jika Anda perlu memastikan menggunakan koneksi 'tenant'
            /*
            Category::on('tenant')->create([ ... ]);
            Unit::on('tenant')->create([ ... ]);
            */
        } else {
            // Jika Super Admin belum ada atau data tenant kosong, throw error atau log.
            // Untuk seeding, kita bisa keluar dari fungsi ini.
            return;
        }
    }
}
