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
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->index(); 
            $table->foreignId('wherehouse_id')->nullable()->constrained('warehouses')->index(); 
            $table->foreignId('warehouse_stock_product_id')->nullable()->constrained('warehouse_stock_products');
            $table->foreignId('sales_id')->nullable()->constrained('sales');
            $table->foreignId('colors_id')->nullable()->constrained('colors');
            $table->foreignId('sizes_id')->nullable()->constrained('sizes');

            $table->text('sequence')->nullable()->index();
            $table->date('expiry_date')->nullable();
            $table->double('quantity')->default(0);
            $table->double('weight')->default(0);
            $table->double('sale_price')->nullable();
            $table->double('purchases_price')->nullable();
            $table->integer('status')->default(0);

            $table->timestamps();

            // ✅ Composite (grouped) index
            $table->index(['outlet_id', 'product_id', 'wherehouse_id', 'status']);
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
