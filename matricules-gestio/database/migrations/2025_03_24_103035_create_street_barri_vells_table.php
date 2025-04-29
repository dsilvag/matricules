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
        Schema::create('street_barri_vells', function (Blueprint $table) {
            $table->integer('CARCOD')->primary();
            $table->foreign('CARCOD')->references('CARCOD')->on('streets')
                ->onDelete('cascade');
            $table->string('user')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('street_barri_vells');
    }
};
