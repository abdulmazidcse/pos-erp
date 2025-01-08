<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::create('app_version_control', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 10); // Android or iOS
            $table->string('latest_version', 10); // e.g., 1.2.0
            $table->string('minimum_version', 10); // e.g., 1.0.0
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
        Schema::dropIfExists('app_version_control');
    }
};
