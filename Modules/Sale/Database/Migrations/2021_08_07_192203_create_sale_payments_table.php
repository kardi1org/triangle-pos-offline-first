<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel sales
            $table->foreignId('sale_id')
                ->constrained('sales') // Sesuaikan jika nama tabel induk berbeda
                ->onDelete('cascade');

            $table->date('date');
            $table->string('reference', 40);
            $table->string('payment_method')->nullable();
            $table->text('note')->nullable();

            // Field nominal pembayaran (Integer sesuai permintaan int(10))
            $table->integer('amount')->nullable();
            $table->integer('cashpay')->nullable();
            $table->integer('debitcard')->nullable();
            $table->integer('creditcard')->nullable();
            $table->integer('gopay')->nullable();
            $table->integer('grabpay')->nullable();
            $table->integer('ovopay')->nullable();
            $table->integer('shopeepay')->nullable();
            $table->integer('danapay')->nullable();
            $table->integer('kredivopay')->nullable();
            $table->integer('qrispay')->nullable();
            $table->integer('change')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_payments');
    }
}
