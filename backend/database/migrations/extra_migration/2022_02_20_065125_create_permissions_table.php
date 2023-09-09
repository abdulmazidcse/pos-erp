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
            $table->id('id');
            $table->unsignedBigInteger('module_id');
            $table->string('name');
            $table->string('slug');
            $table->text('backend_url')->nullable();
            $table->text('frontend_url')->nullable();
            $table->unsignedTinyInteger('column_status')->default(0)->comment("0=default|1=index|2=view|3=create|4=edit|5=delete");
            $table->timestamps();

            $table->unique(['module_id', 'name']);
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
