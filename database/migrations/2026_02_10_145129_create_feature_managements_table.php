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
        Schema::create('feature_managements', function (Blueprint $table) {
            $table->id();
            $table->string('feature_group'); // Contoh: ORDER SUMMARY LOGIC
            $table->string('feature_name');  // Contoh: Service Charge (5%)
            $table->string('feature_key')->unique(); // Contoh: summary_service_charge
            $table->boolean('package_1')->default(0); // Basic
            $table->boolean('package_2')->default(0); // Pro
            $table->boolean('package_3')->default(0); // Premium
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_managements');
    }
};
