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
        Schema::create('camera_street', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('camera_id');
            $table->integer('CARCOD');
            $table->foreign('camera_id')->references('id')->on('cameras')->onDelete('cascade');
            $table->foreign('CARCOD')->references('CARCOD')->on('street_barri_vells')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camera_street');
    }
};
