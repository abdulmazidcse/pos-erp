<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersPointsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_points', function (Blueprint $table) {
            $table->id('id');
            $table->integer('user_id')->unsigned();
            $table->integer('sale_id')->unsigned();
            $table->string('type');
            $table->integer('points');
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
        Schema::drop('users_points');
    }
}
