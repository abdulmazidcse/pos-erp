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
        Schema::table('sales_discounts', function (Blueprint $table) {
            $table->foreign(['sale_item_id'])->references(['id'])->on('sale_items')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_discounts', function (Blueprint $table) {
            $table->dropForeign('sales_discounts_sale_item_id_foreign');
        });
    }
};
