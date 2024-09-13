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
        Schema::create('tbl_inventories', function(Blueprint $table){
            $table->integer('InventoryID')->autoIncrement()->primary();
            $table->string('ItemName');
            $table->double('ItemQty');
            $table->double('ItemVolume');
            $table->double('RemainingVolume');
            $table->double('VolumeUse');
            $table->integer('ShopID');
            $table->foreign('ShopID')->references('ShopID')->on('tbl_shops')->onDelete('restrict')->onUpdate('cascade');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_inventories');
    }
};
