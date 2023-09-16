<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->string('name', 100);
            $table->string('contact_person_name', 100)->nullable();
            $table->string('outlet_number', 20)->nullable();
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('area_id');
            $table->string('police_station', 100);
            $table->string('road_no', 50);
            $table->string('plot_no', 50);
            $table->string('latitude', 50);
            $table->string('longitude', 50);
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
        Schema::dropIfExists('outlets');
    }
}
