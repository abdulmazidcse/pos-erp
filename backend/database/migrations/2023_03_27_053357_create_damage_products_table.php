<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDamageProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('damage_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id')->default(0);
            $table->unsignedBigInteger('warehouse_id')->default(0);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_stock_id');
            $table->unsignedBigInteger('product_unit_id')->nullable();
            $table->date('expires_date')->nullable();
            $table->decimal('damage_quantity', 8,4)->default(0);
            $table->decimal('damage_weight', 8,4)->default(0);
            $table->decimal('cost_price', 8,2);
            $table->text('notes')->nullable();
            $table->tinyInteger('post_status')->default(0)->comment('0=Non-posted|1=Posted');
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
        Schema::dropIfExists('damage_products');
    }
}
