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
            $table->string('PAISPROVMUNICARCOD')->primary();
            $table->foreign('PAISPROVMUNICARCOD')->references('PAISPROVMUNICARCOD')->on('streets')
                ->onDelete('cascade');
            $table->string('user')->nullable();
            $table->boolean('isCamera')->default(false);
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
