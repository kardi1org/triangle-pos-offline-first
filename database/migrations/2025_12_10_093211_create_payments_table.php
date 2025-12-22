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
        Schema::create('payments', function (Blueprint $table) {

            // Kolom id (Primary Key)
            // Menggantikan int(11) NOT NULL di akhir skema Anda dan menjadikannya primary key auto-increment standar Laravel (BIGINT UNSIGNED)
            // Kolom code (int(11) NOT NULL)
            $table->integer('code')->unique(); // Diasumsikan 'code' ini unik

            // Kolom Metode Pembayaran (varchar(1) default 'Y')
            // Menggunakan string(1) dan default 'Y'
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

            // Catatan: Skema SQL Anda tidak menyertakan kolom timestamps.
            // Jika Anda ingin menamambahkannya (disarankan), tambahkan:
            // $table->timestamps();
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
