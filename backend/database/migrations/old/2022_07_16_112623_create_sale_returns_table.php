<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleReturnsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id('id');
            $table->integer('sale_id')->unsigned();
            $table->unsignedBigInteger('voucher_id')->default(0);
            $table->double('return_amount')->nullable();
            $table->string('return_reason')->nullable(); 
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
        Schema::drop('sale_returns');
    }
}
