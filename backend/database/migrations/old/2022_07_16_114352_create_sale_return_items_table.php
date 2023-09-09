<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleReturnItemsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_return_items', function (Blueprint $table) {
            $table->id('id');
            $table->integer('sale_id')->unsigned();
            $table->integer('sale_return_id')->unsigned();
            $table->integer('sale_item_id')->unsigned();
            $table->integer('item_pro_id')->unsigned();
            $table->integer('replace_pro_id')->unsigned();
            $table->decimal('sale_item_qty')->unsigned()->nullable(); 
            $table->decimal('sale_r_qty')->unsigned()->nullable();  
            $table->unsignedTinyInteger('return_type')->default(0)->comment('0=default 1=return 2=replace 3=void');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sale_return_items');
    }
}
