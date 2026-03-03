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
        Schema::create('service_charges', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Service Charge Dine-In
            $table->decimal('percentage', 5, 2); // Simpan angka seperti 5.00
            /**
             * 1: Persentase dari Total Mamin (Bruto)
             * 2: Persentase dari (Total Mamin - Diskon) (Netto)
             */
            $table->tinyInteger('calculation_type')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
