<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Ambil daftar foreign keys yang ada di tabel sales secara manual
        $foreignKeys = collect(\DB::select("
        SELECT CONSTRAINT_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = '" . \DB::getDatabaseName() . "'
        AND TABLE_NAME = 'sales'
        AND CONSTRAINT_NAME = 'sales_user_id_foreign'
    "));

        // 2. Jika ditemukan, baru lakukan drop
        if ($foreignKeys->count() > 0) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropForeign('sales_user_id_foreign');
            });
        }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            //
        });
    }
};
