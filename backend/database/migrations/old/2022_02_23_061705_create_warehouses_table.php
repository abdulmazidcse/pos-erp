<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehousesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('company_id');
            $table->string('name', 100);
            $table->string('contact_person_name', 100)->nullable();
            $table->string('warehouse_number', 20)->nullable();  
            $table->string('address',255); 
            $table->unsignedTinyInteger('status')->default(1)->comment("0=Inactive|1=Active");
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
        Schema::drop('warehouses');
    }
}
