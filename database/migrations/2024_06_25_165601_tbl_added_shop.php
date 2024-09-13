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
        Schema::create('tbl_added_shops', function(Blueprint $table){
            $table->integer('AddedShopID')->autoIncrement()->primary();
            $table->enum('IsValued',['0','1','2']);
            $table->date('Date');
            $table->integer('ShopID');
            $table->integer('CustomerID');
            $table->foreign('ShopID')->references('ShopId')->on('tbl_shops')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('CustomerID')->references('CustomerID')->on('tbl_customers')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_added_shops');
    }
};
