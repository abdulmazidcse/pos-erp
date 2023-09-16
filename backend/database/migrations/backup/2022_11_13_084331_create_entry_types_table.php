<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntryTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('label');
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->unsignedTinyInteger('numbering')->default(1)->comment('1=Auto|2=Manual(required)|3=Manual(optional)');
            $table->string('prefix', 5)->nullable();
            $table->string('suffix', 5)->nullable();
            $table->unsignedTinyInteger('zero_padding')->default(0);
            $table->unsignedTinyInteger('restrictions')->default(1)->comment('1=Unrestricted|2=Debit Account|3=Credit Account|4=Debit and Credit Account|5=Non bank or cash Debit and credit Account');
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
        Schema::dropIfExists('entry_types');
    }
}
