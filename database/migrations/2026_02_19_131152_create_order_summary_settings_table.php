<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_summary_settings', function (Blueprint $table) {
            $table->id();
            $table->string('feature_key')->unique(); // ID internal (contoh: service_charge)
            $table->string('feature_name');          // Nama tampilan (contoh: Biaya Layanan)
            $table->text('formula_description')->nullable();
            $table->enum('tax_position', ['before', 'after'])->default('before');
            $table->decimal('default_value', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_summary_settings');
    }
};
