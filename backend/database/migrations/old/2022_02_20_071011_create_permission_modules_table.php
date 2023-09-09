<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionModulesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_modules', function (Blueprint $table) {
            $table->id('id');
            $table->string('name')->unique();
            $table->string('slug');
            $table->string('icon_name')->nullable();
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->tinyInteger('is_action_menu')->default(0);
            $table->tinyInteger('is_multiple_action')->default(0);
            $table->tinyInteger('is_children')->default(0);
            $table->unsignedInteger('menu_order')->default(1);
            $table->unsignedInteger('total_actions')->default(0);
            $table->tinyInteger('columnable_permission')->default(0)->comment("0=No|1=Yes");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permission_modules');
    }
}
