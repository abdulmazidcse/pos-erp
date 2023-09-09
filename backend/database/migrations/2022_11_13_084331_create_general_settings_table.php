<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('invoice_sms_status')->default(true)->comment('1=Active 0=In-Active');
            $table->unsignedTinyInteger('payment_status')->default(1)->comment('1=Active 0=In-Active');
            $table->unsignedTinyInteger('date_status')->default(1)->comment('1=Active 0=In-Active');
            $table->text('date_format')->nullable();
            $table->text('api_key')->nullable();
            $table->text('sender_id')->nullable();
            $table->text('sms_text')->nullable();
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
        Schema::dropIfExists('general_settings');
    }
}
