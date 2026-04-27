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
        // Tabel Transaksi Utama
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->string('reference'); // Contoh: MVT-20260415-0001
            $table->date('date');
            $table->unsignedBigInteger('from_warehouse_id');
            $table->unsignedBigInteger('to_warehouse_id');
            $table->unsignedBigInteger('user_id'); // Operator yang input
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // Tabel Detail Item Mutasi
        Schema::create('inventory_movement_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_movement_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->timestamps();
        });
    }
};
