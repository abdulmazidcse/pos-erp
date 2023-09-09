<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code',40);
            $table->integer('parent_id')->default(0);
            $table->mediumText('description')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->integer('order')->unsigned()->default(0);
            $table->string('image', 255)->nullable();
            $table->integer('company_id')->unsigned()->default(0);
            $table->tinyInteger('is_featured')->unsigned()->default(0);
            $table->integer('discount')->unsigned()->default(0);
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
        Schema::dropIfExists('product_categories');
    }
}
