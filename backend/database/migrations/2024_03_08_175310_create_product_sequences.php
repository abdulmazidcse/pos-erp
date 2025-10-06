<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSequences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_sequences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->unsignedBigInteger('stock_product_id')->nullable();
            $table->unsignedBigInteger('sales_id')->nullable();
            $table->unsignedBigInteger('colors_id')->nullable();
            $table->unsignedBigInteger('sizes_id')->nullable();
            $table->string('sequence')->nullable();
            $table->date('expiry_date')->nullable();
            $table->double('quantity')->default(0);
            $table->double('weight')->default(0);
            $table->double('sale_price')->nullable();
            $table->double('purchases_price')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();

            $table->index(['outlet_id', 'product_id', 'sequence', 'status'], 'ws_outlet_product_ware_status_idx');

            // Optional foreign key constraints (uncomment if needed)
            // $table->foreign('product_id')->references('id')->on('products');
            // $table->foreign('outlet_id')->references('id')->on('outlets');
            // $table->foreign('sales_id')->references('id')->on('sales');
            // $table->foreign('colors_id')->references('id')->on('colors');
            // $table->foreign('sizes_id')->references('id')->on('sizes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_sequences');
    }
}
