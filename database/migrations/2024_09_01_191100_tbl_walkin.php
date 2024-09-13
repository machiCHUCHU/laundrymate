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
        Schema::create('tbl_walkins', function(Blueprint $table){
            $table->integer('WalkinID')->autoIncrement()->primary();
            $table->string('ContactNumber');
            $table->double('WalkinLoad');
            $table->double('Total');
            $table->dateTime('DateIssued');
            $table->enum('Status', ['0','1','2','3','4','5']);
            $table->enum('PaymentStatus', ['pending', 'paid']);
            $table->integer('ServiceID');
            $table->integer('ShopID');
            $table->foreign('ServiceID')->references('ServiceID')->on('tbl_laundry_services')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ShopID')->references('ShopID')->on('tbl_shops')->onDelete('restrict')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
