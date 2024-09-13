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
        Schema::create('tbl_bookings', function(Blueprint $table){
            $table->integer('BookingID')->autoIncrement()->primary();
            $table->double('CustomerLoad');
            $table->double('LoadCost');
            $table->dateTime('Schedule');
            $table->date('DateIssued');
            $table->enum('Status',['0','1','2','3','4','5','6']);
            $table->enum('PaymentStatus',['pending', 'paid']);
            $table->integer('CustomerID');
            $table->integer('ShopID');
            $table->integer('ServiceID');
            $table->foreign('CustomerID')->references('CustomerID')->on('tbl_customers')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ShopID')->references('ShopID')->on('tbl_shops')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ServiceID')->references('ServiceID')->on('tbl_laundry_services')->onDelete('restrict')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_bookings');
    }
};
