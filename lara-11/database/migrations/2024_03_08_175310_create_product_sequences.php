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
            $table->bigIncrements('id'); 
            $table->string('sequence', 100);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedTinyInteger('status')->default(1)->comment('0=in stock|1=sold');
            $table->unsignedBigInteger('sale_item_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['product_id'])->references(['id'])->on('products')->onDelete('CASCADE');
            $table->foreign(['purchase_order_id'])->references(['id'])->on('purchase_receives')->onDelete('CASCADE');
            $table->foreign(['sale_item_id'])->references(['id'])->on('sale_items')->onDelete('CASCADE');
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
