<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('module_id');
            $table->string('name');
            $table->string('slug');
            $table->string('url_path')->nullable();
            $table->string('component_path')->nullable();
            $table->text('backend_url')->nullable();
            $table->text('frontend_url')->nullable();
            $table->unsignedTinyInteger('column_status')->default(0)->comment('0=default|1=index|2=view|3=create|4=edit|5=delete|6=others');
            $table->unsignedTinyInteger('is_route_action')->default(0)->comment('0=Only Action|1=Route and Action');
            $table->tinyInteger('is_nav')->default(0)->comment('0=Do not added menu|1=This Added menu');
            $table->tinyInteger('is_index')->default(0);
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
