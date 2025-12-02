<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('order_type', 20)->default('dine_in')->after('customer_name');
            $table->unsignedBigInteger('table_id')->nullable()->after('order_type');

            $table->foreign('table_id')->references('id')->on('mejas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['table_id']);
            $table->dropColumn(['order_type', 'table_id']);
        });
    }
};
