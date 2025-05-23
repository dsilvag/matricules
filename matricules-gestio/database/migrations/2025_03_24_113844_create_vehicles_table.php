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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('MATRICULA');
            $table->date('DATAEXP')->nullable();
            $table->date('DATAINICI')->nullable();
            $table->unsignedBigInteger('instance_id')->nullable();
            $table->foreign('instance_id')->references('id')->on('instances')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
