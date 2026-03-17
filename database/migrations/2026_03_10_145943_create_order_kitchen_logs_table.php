<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_kitchen_logs', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('sale_id')->constrained()->onDelete('cascade');
            $blueprint->string('reference'); // Nomor nota (e.g., AS-0001)
            $blueprint->string('product_name');
            $blueprint->integer('qty'); // Jumlah selisih
            $blueprint->enum('type', ['new', 'void']); // Jenis log
            $blueprint->string('note')->nullable(); // Alasan (e.g., Pengurangan Qty)
            $blueprint->foreignId('user_id')->constrained(); // Siapa yang melakukan edit
            $blueprint->boolean('is_printed')->default(false); // Status sudah cetak ke dapur
            $blueprint->string('approved_by');
            $blueprint->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_kitchen_logs');
    }
};
