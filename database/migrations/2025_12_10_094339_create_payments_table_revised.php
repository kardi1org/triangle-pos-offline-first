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
        // Pastikan tabel lama dihapus sebelum dibuat ulang
        Schema::dropIfExists('payments');

        Schema::create('payments', function (Blueprint $table) {

            // Kolom code (int(11) NOT NULL) sebagai Primary Key
            // Ini menggantikan fungsi kolom id standar Laravel.
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

            // Catatan: Kolom created_at dan updated_at tidak ditambahkan
            // agar sesuai persis dengan skema SQL asli Anda.
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
