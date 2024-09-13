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
        Schema::create('tbl_ratings', function(Blueprint $table){
            $table->integer('RatingID')->autoIncrement()->primary();
            $table->enum('Rate',['1','2','3','4','5']);
            $table->text('Comment');
            $table->date('DateIssued');
            $table->integer('BookingID');
            $table->integer('ShopID');
            $table->integer('CustomerID');
            $table->foreign('BookingID')->references('BookingID')->on('tbl_bookings')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ShopID')->references('ShopID')->on('tbl_shops')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('CustomerID')->references('CustomerID')->on('tbl_customers')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_ratings');
    }
};
