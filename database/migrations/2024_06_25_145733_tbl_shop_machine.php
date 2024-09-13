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
        Schema::create('tbl_shop_machines', function(Blueprint $table){
            $table->integer('ShopMachineID')->autoIncrement()->primary();
            $table->integer('WasherQty');
            $table->integer('WasherTime');
            $table->integer('DryerQty');
            $table->integer('DryerTime');
            $table->integer('FoldingTime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_shop_machines');
    }
};
