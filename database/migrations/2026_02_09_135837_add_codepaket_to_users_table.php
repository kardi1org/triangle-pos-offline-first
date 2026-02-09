<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $col) {
            // Menambahkan kolom codepaket setelah kolom email (opsional)
            // nullable() digunakan agar user yang sudah ada tidak error karena kolom kosong
            $col->string('codepaket')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $col) {
            $col->dropColumn('codepaket');
        });
    }
};
