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
        Schema::create('vehicle_street', function (Blueprint $table) {
            $table->id();
            $table->string('MATRICULA');  // Explicitly define the foreign key for Vehicle
            $table->integer('CARCOD');  // Explicitly define the foreign key for StreetBarriVell
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('MATRICULA')->references('MATRICULA')->on('vehicles')->onDelete('cascade');
            $table->foreign('CARCOD')->references('CARCOD')->on('street_barri_vells')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_street');
    }
};
