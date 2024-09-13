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
        Schema::create('tbl_owners', function(Blueprint $table){
            $table->integer('OwnerID')->autoIncrement()->primary();
            $table->string('Name');
            $table->enum('Sex',['male','female']);
            $table->string('Address');
            $table->timestamp('VerifiedAt')->useCurrent();
            $table->string('Image')->nullable();
            $table->string('ContactNumber');
            $table->foreign('ContactNumber')->references('contact')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_owners');
    }
};
