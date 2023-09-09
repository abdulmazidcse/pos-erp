<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductGroupsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_groups', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('product_id');
            $table->integer('group_id');
            $table->unsignedBigInteger('attribute_id');
            $table->unsignedBigInteger('attribute_value_id');
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
        Schema::drop('product_groups');
    }
}
