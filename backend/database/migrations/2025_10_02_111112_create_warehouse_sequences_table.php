<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouse_sequences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('products')->index();

            $table->foreignId('outlet_id')->nullable()->index();
            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade')->name('fk_warehouse_sequences_outlet_id');

            $table->foreignId('warehouse_id')->nullable()->index();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade')->name('fk_warehouse_sequences_warehouse_id');

            $table->foreignId('warehouse_stock_product_id')->nullable()->constrained('warehouse_stock_products');
            $table->foreignId('sales_id')->nullable()->constrained('sales');
            $table->foreignId('colors_id')->nullable()->constrained('colors');
            $table->foreignId('sizes_id')->nullable()->constrained('sizes');

            $table->string('sequence', 100)->nullable()->index();

            $table->date('expiry_date')->nullable();
            $table->double('quantity')->default(0);
            $table->double('weight')->default(0);
            $table->double('sale_price')->nullable();
            $table->double('purchases_price')->nullable();
            $table->integer('status')->default(0);

            $table->timestamps();

            // ✅ Composite (grouped) index
            // $table->index(['outlet_id', 'product_id', 'warehouse_id', 'status']);
            $table->index(['outlet_id', 'product_id', 'warehouse_id', 'status'], 'ws_outlet_product_ware_status_idx');

        });

    }

    /**
     * Reverse the migrations.   
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_sequences');
    }
};
