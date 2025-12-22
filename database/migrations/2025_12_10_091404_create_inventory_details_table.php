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
        Schema::create('inventory_details', function (Blueprint $table) {
            // Kolom id (Primary Key)
            $table->id(); // Membuat kolom 'id' sebagai BIGINT(20) UNSIGNED dan AUTO_INCREMENT PRIMARY KEY

            // Foreign Keys
            // Inventory_id (Perhatikan huruf besar/kecil di sini, disarankan inventory_id)
            $table->foreignId('Inventory_id')->nullable();
            // product_id
            $table->foreignId('product_id')->nullable();

            // Kolom Detail Produk
            $table->string('product_name')->nullable();
            $table->string('product_code')->nullable();

            // Kolom Harga dan Jumlah (int)
            $table->integer('quantity')->nullable();
            $table->integer('price')->nullable();
            $table->integer('unit_price')->nullable();
            $table->integer('sub_total')->nullable();

            // Kolom Diskon Produk
            $table->integer('product_discount_amount')->nullable();
            // Kolom tipe diskon dengan default 'fixed'
            $table->string('product_discount_type')->default('fixed');

            // Kolom Pajak Produk
            $table->integer('product_tax_amount')->nullable();

            // Kolom created_at dan updated_at
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
        Schema::dropIfExists('inventory_details');
    }
};
