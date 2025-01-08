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
        Schema::create('sale_return_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('sale_id');
            $table->unsignedInteger('sale_return_id');
            $table->unsignedInteger('sale_item_id');
            $table->unsignedInteger('item_pro_id');
            $table->unsignedInteger('replace_pro_id');
            $table->double('sale_item_qty')->unsigned()->nullable()->default(0);
            $table->double('sale_r_qty')->unsigned()->nullable()->default(0);
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
        Schema::dropIfExists('sale_return_items');
    }
};
