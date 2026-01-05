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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->dateTime('open_time');
            $table->dateTime('close_time')->nullable();
            $table->decimal('starting_cash', 15, 2)->default(0);
            $table->decimal('ending_cash', 15, 2)->nullable();
            $table->decimal('expected_ending_cash', 15, 2)->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
