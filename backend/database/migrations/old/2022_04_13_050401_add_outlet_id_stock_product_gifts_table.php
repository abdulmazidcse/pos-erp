<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOutletIdStockProductGiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_product_gifts', function (Blueprint $table) {
           $table->unsignedBigInteger('outlet_id')->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_product_gifts', function (Blueprint $table) {
            $table->dropColumn('outlet_id');
        });
    }
}
