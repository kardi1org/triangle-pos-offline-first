<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            // Kolom id
            $table->id(); // Membuat kolom 'id' sebagai BIGINT(20) UNSIGNED dan AUTO_INCREMENT PRIMARY KEY

            // Kolom amount
            // Menggunakan integer() untuk INT(11)
            $table->integer('amount');

            // Kolom date
            $table->date('date');

            // Kolom details
            // Menggunakan text() untuk TEXT DEFAULT NULL
            $table->text('details')->nullable();

            // Kolom created_at dan updated_at
            $table->timestamps(); // Membuat kolom 'created_at' dan 'updated_at' TIMESTAMP NULL
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('budgets');
    }
};
