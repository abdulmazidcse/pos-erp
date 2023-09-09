<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // companies table
        Schema::create('companies', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 100)->unique();
            $table->string('logo')->default('company-logo.png');
            $table->string('address', 255);
            $table->string('contact_person_name', 120)->nullable();
            $table->string('contact_person_number', 20)->nullable();
            $table->unsignedTinyInteger('status')->default(0)->comment('0=Inactive|1=Active');
            $table->timestamps();
            $table->softDeletes();
        });

        // departments table
        Schema::create('departments', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 60);
            $table->unsignedInteger('company_id')->default(0);
            $table->unsignedTinyInteger('status')->default(0)->comment('0=Inactive|1=Active');
            $table->timestamps();
            $table->softDeletes();
        });

        //outlets table
        Schema::create('outlets', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('company_id');
            $table->string('name', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address', 255);
            $table->string('outlet_image')->default('outlet-image.png');
            $table->unsignedTinyInteger('status')->default(1)->comment("0=Inactive|1=Active");
            $table->timestamps();
            $table->softDeletes();

        });

        // product_categories table
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('parent_id')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('status', 60)->default('published');
            $table->integer('order')->unsigned()->default(0);
            $table->string('image', 255)->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->tinyInteger('is_featured')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // brands table
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->mediumText('description')->nullable();
            $table->string('website', 255)->nullable();
            $table->string('logo', 255)->nullable();
            $table->string('status', 60)->default('published');
            $table->tinyInteger('order')->unsigned()->default(0);
            $table->integer('company_id')->unsigned()->nullable();
            $table->tinyInteger('is_featured')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // units table
        Schema::create('units', function (Blueprint $table) {
            $table->id('id');
            $table->string('unit_code', 50);
            $table->string('unit_name', 100);
            $table->integer('base_unit', false, true)->nullable();
            $table->string('operator', 5)->nullable();
            $table->double('operation_value')->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('0=Inactive|1=Active');
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
        Schema::dropIfExists('companies');
        Schema::dropIfExists('outlets');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('units');
    }
}
