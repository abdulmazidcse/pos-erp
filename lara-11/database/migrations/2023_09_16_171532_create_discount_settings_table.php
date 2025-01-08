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
        Schema::create('discount_settings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('customer_wise')->nullable();
            $table->integer('customer_group_wise')->nullable();
            $table->integer('individual_customer')->nullable();
            $table->json('customer_offer_within_range')->nullable();
            $table->integer('product_wise')->nullable();
            $table->json('product_offer_within_range')->nullable();
            $table->integer('category_wise')->nullable();
            $table->json('category_offer_within_range')->nullable();
            $table->integer('sub_category_wise')->nullable();
            $table->json('sub_cat_offer_within_range')->nullable();
            $table->integer('vendor_wise')->nullable();
            $table->json('vendor_offer_within_range')->nullable();
            $table->integer('slow_moving_product')->nullable();
            $table->integer('slow_moving_product_discount')->nullable();
            $table->json('slow_moving_offer_within_range')->nullable();
            $table->integer('fast_moving_product')->nullable();
            $table->integer('fast_moving_product_discount')->nullable();
            $table->json('fast_moving_offer_within_range')->nullable();
            $table->integer('sales_platform')->nullable();
            $table->integer('sales_platform_pos')->nullable();
            $table->integer('sales_platform_pos_discount')->nullable();
            $table->json('sales_platform_pos_offer_within_range')->nullable();
            $table->integer('sales_platform_ecom')->nullable();
            $table->integer('sales_platform_ecom_discount')->nullable();
            $table->json('sales_platform_ecom_offer_within_range')->nullable();
            $table->integer('gp_wise')->nullable();
            $table->integer('gp_wise_discount')->nullable();
            $table->json('gp_offer_within_range')->nullable();
            $table->integer('enable_conditional_discount')->nullable();
            $table->json('discount_within_range')->nullable();
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
        Schema::dropIfExists('discount_settings');
    }
};
