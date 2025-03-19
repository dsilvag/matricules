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
        Schema::create('dwellings', function (Blueprint $table) {
            $table->integer('DOMCOD')->primary();
            $table->integer('PAISCOD')->nullable();
            $table->integer('PROVCOD')->nullable();
            $table->integer('MUNICOD')->nullable();
            $table->integer('CARCOD');
            $table->foreign('CARCOD')->references('CARCOD')->on('streets')
                ->onDelete('cascade');
            $table->integer('PSEUDOCOD')->nullable();
            $table->string('GISCOD',255)->nullable();
            $table->char('DOMNUM',4)->nullable();
            $table->char('DOMBIS',1)->nullable();
            $table->char('DOMNUM2',4)->nullable();
            $table->char('DOMBIS2',1)->nullable();
            $table->char('DOMESC',2)->nullable();
            $table->char('DOMPIS',3)->nullable();
            $table->char('DOMPTA',4)->nullable();
            $table->char('DOMBLOC',2)->nullable();
            $table->char('DOMPTAL',2)->nullable();
            $table->integer('DOMKM')->nullable();
            $table->integer('DOMHM')->nullable();
            $table->char('DOMTLOC',1)->nullable();
            $table->integer('APCORREUS')->nullable();
            $table->char('DOMTIP',4)->nullable();
            $table->string('DOMOBS',256)->nullable();
            $table->char('VALDATA',8)->nullable();
            $table->char('BAIXASW',1)->nullable();
            $table->char('STDAPLADD',5)->nullable();
            $table->char('STDAPLMOD',5)->nullable();
            $table->char('STDUGR',20)->nullable();
            $table->char('STDUMOD',20)->nullable();
            $table->char('STDDGR',8)->nullable();
            $table->char('STDDMOD',8)->nullable();
            $table->char('STDHGR',6)->nullable();
            $table->char('STDHMOD',6)->nullable();
            $table->string('DOMCP',20)->nullable();
            $table->float('X')->nullable();
            $table->float('Y')->nullable();
            $table->string('POBLDESC',50)->nullable();
            $table->string('GID',32)->nullable();
            $table->integer('SWREVISAT')->nullable();
            $table->string('REFCADASTRAL',255)->nullable();
            $table->integer('SWPARE')->nullable();
            $table->char('CIV',24)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dwellings');
    }
};
