<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_outlets', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('outlet_id');

            // FOREIGN KEY CONSTRAINS
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade');

            // SETTING THE PRIMARY KEY
            $table->primary(['user_id', 'outlet_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_outlets');
    }
}
