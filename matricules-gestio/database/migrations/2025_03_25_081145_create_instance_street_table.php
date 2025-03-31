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
        Schema::create('instance_street', function (Blueprint $table) {
            $table->id();
            $table->string('RESNUME');  
            $table->integer('CARCOD');  
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('RESNUME')->references('RESNUME')->on('instances')->onDelete('cascade');
            $table->foreign('CARCOD')->references('CARCOD')->on('street_barri_vells')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instance_street');
    }
};
