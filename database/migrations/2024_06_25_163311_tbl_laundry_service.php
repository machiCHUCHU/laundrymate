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
        Schema::create('tbl_laundry_services', function(Blueprint $table){
            $table->integer('ServiceID')->autoIncrement()->primary();
            $table->string('ServiceName');
            $table->integer('LoadWeight');
            $table->double('LoadPrice');
            $table->integer('ShopID');
            $table->foreign('ShopID')->references('ShopId')->on('tbl_shops')->onDelete('restrict')->onUpdate('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_laundry_services');
    }
};
