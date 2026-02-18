<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Run: php artisan make:migration add_extra_charges_to_sales_table --table=sales

    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->bigInteger('service_charge')->default(0)->after('shipping_amount');
            $table->bigInteger('lain_a')->default(0)->after('service_charge');
            $table->bigInteger('lain_b')->default(0)->after('lain_a');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['service_charge', 'lain_a', 'lain_b']);
        });
    }
};
