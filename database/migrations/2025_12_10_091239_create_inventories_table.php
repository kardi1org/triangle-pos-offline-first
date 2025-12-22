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
        Schema::create('inventories', function (Blueprint $table) {
            // Kolom id (Primary Key)
            $table->id(); // Membuat kolom 'id' sebagai BIGINT(20) UNSIGNED dan AUTO_INCREMENT PRIMARY KEY

            // Kolom date
            $table->date('date')->nullable();

            // Kolom reference
            $table->string('reference')->nullable();

            // Kolom supplier_id (Foreign Key Potential)
            $table->foreignId('supplier_id')->nullable(); // Setara dengan BIGINT(20) UNSIGNED DEFAULT NULL

            // Kolom supplier_name
            $table->string('supplier_name')->nullable();

            // Kolom diskon, pajak, dan biaya (Menggunakan tipe integer dengan nilai default)
            $table->integer('tax_percentage')->default(0);
            $table->integer('tax_amount')->default(0);
            $table->integer('discount_percentage')->default(0);
            $table->integer('discount_amount')->default(0);
            $table->integer('shipping_amount')->default(0);

            // Kolom Jumlah Total (int)
            $table->integer('total_amount')->nullable();
            $table->integer('paid_amount')->nullable();
            $table->integer('due_amount')->nullable();

            // Kolom Status (varchar)
            $table->string('status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_method')->nullable();

            // Kolom note (text)
            $table->text('note')->nullable();

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
        Schema::dropIfExists('inventories');
    }
};
