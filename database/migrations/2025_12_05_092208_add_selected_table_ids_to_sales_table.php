<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Menggunakan 'text' untuk menyimpan array ID yang di-encode sebagai JSON
            $table->text('selected_table_ids')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('selected_table_ids');
        });
    }
};
