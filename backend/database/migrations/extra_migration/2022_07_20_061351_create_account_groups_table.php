<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountGroupsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_groups', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('group_code')->unique();
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->string('group_name');
            $table->unsignedInteger('order')->default(1);
            $table->unsignedTinyInteger('status')->default(1)->comment("1=Active|0=Inactive");
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
        Schema::drop('account_groups');
    }
}
