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
        Schema::create('telecos', function (Blueprint $table) {
            $table->integer('PERSCOD');
            $table->integer('NUMORDRE');
            $table->foreign('PERSCOD')->references('PERSCOD')->on('people')
                ->onDelete('cascade');
            $table->char('TIPCONTACTE',4)->nullable();
            $table->string('CONTACTE',255)->nullable();
            $table->string('OBSERVACIONS',255)->nullable();
            $table->char('STDUGR',20)->nullable();
            $table->char('STDUMOD',20)->nullable();
            $table->char('STDDGR',8)->nullable();
            $table->char('STDDMOD',8)->nullable();
            $table->char('STDHGR',6)->nullable();
            $table->char('STDHMOD',6)->nullable();
            $table->char('VALDATA',8)->nullable();
            $table->char('BAIXASW',1)->nullable();
            $table->primary(['PERSCOD', 'NUMORDRE']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telecos');
    }
};
