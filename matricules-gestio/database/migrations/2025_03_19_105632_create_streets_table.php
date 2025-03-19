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
        Schema::create('streets', function (Blueprint $table) {
            $table->integer('PAISCOD');
            $table->integer('PROVCOD');
            $table->integer('MUNICOD');
            $table->integer('CARCOD')->primary();
            $table->char('CARSIG',5)->nullable();
            $table->char('CARPAR',6)->nullable();
            $table->string('CARDESC',50);
            $table->string('CARDESC2',25)->nullable();
            $table->char('STDUGR',20)->nullable();
            $table->char('STDUMOD',20)->nullable();
            $table->char('STDDGR',8)->nullable();
            $table->char('STDDMOD',8)->nullable();
            $table->char('STDHGR',6)->nullable();
            $table->char('STDHMOD',6)->nullable();
            $table->char('VALDATA',8)->nullable();
            $table->char('BAIXASW',1)->nullable();
            $table->string('INICIFI',4000)->nullable();
            $table->string('OBSERVACIONS',4000)->nullable();
            $table->char('ORGCOD',4)->nullable();
            $table->char('ORGDATA',8)->nullable();
            $table->string('ORGOBS',4000)->nullable();
            $table->string('PLACA',255)->nullable();
            $table->string('GENERIC',50)->nullable();
            $table->string('ESPECIFIC',50)->nullable();
            $table->string('TEMATICA',50)->nullable();
            $table->char('SEXE',1)->nullable();
            $table->char('LOCAL',1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streets');
    }
};
