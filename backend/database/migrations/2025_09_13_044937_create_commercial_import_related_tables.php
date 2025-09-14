<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportRelatedTables extends Migration
{
    public function up(): void
    {
        Schema::create('performa_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('pi_number', 50)->unique();
            $table->unsignedBigInteger('vendor_id');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->enum('shipment_type', ['sea', 'air']);
            $table->decimal('total_amount_usd', 15, 2);
            $table->decimal('total_amount_local', 15, 2);
            $table->string('currency_code', 3);
            $table->decimal('exchange_rate', 10, 4);
            $table->enum('status', ['draft', 'sent', 'accepted', 'amended']);
            $table->boolean('is_part_shipment')->default(false);
            $table->timestamps();
        });

        Schema::create('letter_of_credits', function (Blueprint $table) {
            $table->id();
            $table->string('lc_number', 100)->unique();
            $table->unsignedBigInteger('pi_id');
            $table->unsignedBigInteger('bank_id');
            $table->date('opening_date');
            $table->date('expiry_date');
            $table->decimal('amount', 15, 2);
            $table->decimal('margin_percentage', 5, 2);
            $table->decimal('bank_charges', 10, 2);
            $table->enum('status', ['opened', 'amended', 'closed']);
            $table->string('document_path', 255)->nullable();
            $table->timestamps();

            $table->foreign('pi_id')->references('id')->on('performa_invoices')->onDelete('cascade');
        });

        Schema::create('cost_sheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lc_id');
            $table->unsignedBigInteger('product_id');
            $table->json('cost_elements');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->timestamps();

            $table->foreign('lc_id')->references('id')->on('letter_of_credits')->onDelete('cascade');
        });

        Schema::create('pipeline_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pi_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2);
            $table->boolean('is_serialized');
            $table->enum('status', ['in_transit', 'received', 'partial']);
            $table->timestamps();

            $table->foreign('pi_id')->references('id')->on('performa_invoices')->onDelete('cascade');
        });

        Schema::create('import_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pipeline_stock_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2);
            $table->date('received_date');
            $table->timestamps();

            $table->foreign('pipeline_stock_id')->references('id')->on('pipeline_stocks')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_stocks');
        Schema::dropIfExists('pipeline_stocks');
        Schema::dropIfExists('cost_sheets');
        Schema::dropIfExists('letter_of_credits');
        Schema::dropIfExists('performa_invoices');
    }
}

