<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToProductsSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_suppliers', function (Blueprint $table) {
            $table->foreign(['supplier_id'])->references(['id'])->on('suppliers')->onDelete('CASCADE');
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
        Schema::table('products_suppliers', function (Blueprint $table) {
            $table->dropForeign('products_suppliers_supplier_id_foreign');
            $table->dropForeign('products_suppliers_product_id_foreign');
        });
    }
}
