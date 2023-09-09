<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToProductsSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_sizes', function (Blueprint $table) {
            $table->foreign(['size_id'])->references(['id'])->on('sizes')->onDelete('CASCADE');
            $table->foreign(['product_id'])->references(['id'])->on('products')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_sizes', function (Blueprint $table) {
            $table->dropForeign('products_sizes_size_id_foreign');
            $table->dropForeign('products_sizes_product_id_foreign');
        });
    }
}
