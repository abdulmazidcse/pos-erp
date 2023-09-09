<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSizesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_sizes', function (Blueprint $table) {  
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('size_id');  

            // FOREIGN KEY CONSTRAINS
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');

            $table->primary(['product_id', 'size_id']); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products_sizes');
    }
}
