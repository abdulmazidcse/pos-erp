<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSuppliersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_suppliers', function (Blueprint $table) { 
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('supplier_id');  

            // FOREIGN KEY CONSTRAINS
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');

            $table->primary(['product_id', 'supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products_suppliers');
    }
}
