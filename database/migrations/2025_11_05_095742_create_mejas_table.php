<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mejas', function (Blueprint $table) {
            $table->id();
            $table->integer('no_meja');
            $table->string('name', 100)->nullable();
            $table->integer('qty_pax')->nullable();
            $table->string('location', 100)->nullable();
            $table->string('shape', 50)->nullable();
            $table->integer('status')->nullable(); // 0=available, 1=occupied, dll
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mejas');
    }
};
