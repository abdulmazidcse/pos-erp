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
        Schema::table('products_colors', function (Blueprint $table) {
            $table->foreign(['product_id'])->references(['id'])->on('products')->onDelete('CASCADE');
            $table->foreign(['color_id'])->references(['id'])->on('colors')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_colors', function (Blueprint $table) {
            $table->dropForeign('products_colors_product_id_foreign');
            $table->dropForeign('products_colors_color_id_foreign');
        });
    }
};
