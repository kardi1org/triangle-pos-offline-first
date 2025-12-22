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
        // 1. Drop tabel lama untuk memastikan skema baru yang digunakan
        Schema::dropIfExists('payments');

        // 2. Buat tabel baru sesuai skema SQL yang diminta
        Schema::create('payments', function (Blueprint $table) {

            // Kolom code (int(11) NOT NULL) sebagai Primary Key
            // Menggunakan integer() dan primary() untuk menjadikannya Primary Key
            // Tidak menggunakan autoIncrement() karena tidak ada di skema SQL asli
            $table->integer('code')->primary();

            // Kolom Metode Pembayaran (varchar(1) default 'Y')
            $table->string('Cash', 1)->default('Y');
            $table->string('DebitCard', 1)->default('Y');
            $table->string('Gopay', 1)->default('Y');
            $table->string('CreditCard', 1)->default('Y');
            $table->string('OVO', 1)->default('Y');
            $table->string('ShopeePay', 1)->default('Y');
            $table->string('Kredivo', 1)->default('Y');
            $table->string('Dana', 1)->default('Y');
            $table->string('GrabPay', 1)->default('Y');
            $table->string('QRIS', 1)->default('Y');

            // Kolom id (int(11) NOT NULL)
            // Ini akan menjadi kolom int biasa yang TIDAK auto-increment
            $table->integer('id');

            // Catatan: Tidak ada created_at/updated_at karena tidak ada timestamps di skema SQL asli.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
