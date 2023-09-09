<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->mediumText('description')->nullable();
            $table->string('website', 255)->nullable();
            $table->string('logo', 255)->nullable();
            $table->tinyInteger('status')->unsigned()->default(0);
            $table->tinyInteger('order')->unsigned()->default(0);
            $table->integer('company_id')->unsigned()->default(0);
            $table->tinyInteger('is_featured')->unsigned()->default(0);
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
        Schema::dropIfExists('brands');
    }
}
