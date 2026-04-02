<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrasi ini akan berjalan di database tenant yang sedang aktif
        Schema::table('sales', function (Blueprint $col) {
            // Kita gunakan unsignedBigInteger karena ID outlet di DB pusat biasanya BigInt
            // Kita tidak menggunakan foreign key constraint (->constrained())
            // karena tabel outlet berada di database yang berbeda (db_pos pusat)
            $col->unsignedBigInteger('outlet_id')->nullable()->after('id');

            // Tambahkan index agar pencarian/filter laporan per outlet lebih cepat
            $col->index('outlet_id');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $col) {
            $col->dropColumn('outlet_id');
        });
    }
};
