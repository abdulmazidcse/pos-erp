<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points_settings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('enable_points_rewards')->nullable();
            $table->integer('enable_signup_points')->nullable();
            $table->integer('signup_points')->nullable();
            $table->integer('enable_referral_points')->nullable();
            $table->integer('referral_points')->nullable();
            $table->integer('enable_social_point')->nullable();
            $table->integer('social_share_points')->nullable();
            $table->integer('social_share_facebook')->nullable();
            $table->integer('social_share_twitter')->nullable();
            $table->integer('custom_points_on_cart')->nullable();
            $table->integer('cart_points_rate')->nullable();
            $table->integer('cart_price_rate')->nullable();
            $table->integer('enable_points_order_total')->nullable(); 
            $table->text('points_within_order_range')->nullable();
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
        Schema::dropIfExists('points_settings');  
    }
}
