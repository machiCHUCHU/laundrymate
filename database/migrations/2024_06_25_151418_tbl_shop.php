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

        Schema::create('tbl_shops', function(Blueprint $table){
            $table->integer('ShopID')->autoIncrement()->primary();
            $table->string('ShopName');
            $table->string('ShopAddress');
            $table->integer('MaxLoad');
            $table->integer('RemainingLoad');
            $table->string('WorkDay');
            $table->string('WorkHour');
            $table->enum('ShopStatus',['closed','open','full']);
            $table->string('ShopCode');
            $table->integer('ShopMachineID');
            $table->integer('OwnerID');
            $table->foreign('ShopMachineID')->references('ShopMachineID')->on('tbl_shop_machines')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('OwnerID')->references('OwnerID')->on('tbl_owners')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_shops');
    }
};
