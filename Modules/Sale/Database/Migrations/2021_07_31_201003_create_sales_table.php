<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reference');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->integer('tax_percentage')->default(0);
            $table->integer('tax_amount')->default(0);
            $table->integer('discount_percentage')->nullable();
            $table->integer('discount_amount')->nullable();
            $table->integer('shipping_amount')->nullable();
            $table->integer('total_amount')->nullable();
            $table->integer('paid_amount')->nullable();
            $table->integer('due_amount')->nullable();
            $table->integer('change')->nullable();
            $table->string('status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('note')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
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
        Schema::dropIfExists('sales');
    }
}
