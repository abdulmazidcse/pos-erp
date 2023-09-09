<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductColorsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_colors', function (Blueprint $table) { 
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('color_id');  

            // FOREIGN KEY CONSTRAINS
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');

            $table->primary(['product_id', 'color_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products_colors');
    }
}
