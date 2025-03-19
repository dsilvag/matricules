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
        Schema::create('people', function (Blueprint $table) {
            $table->integer('PERSCOD')->primary();
            $table->integer('PAISCOD')->nullable();
            $table->integer('PROVCOD')->nullable();
            $table->integer('MUNICOD')->nullable();
            $table->string('PERSNOM',255);
            $table->string('PERSCOG1',25)->nullable();
            $table->string('PERSCOG2',25)->nullable();
            $table->char('PERSPAR1',6)->nullable();
            $table->char('PERSPAR2',6)->nullable();
            $table->char('NIFNUMP',10)->nullable();
            $table->char('NIFNUM',8)->nullable();
            $table->char('NIFDC',1)->nullable();
            $table->char('NIFSW',1);
            $table->char('PERSDCONNIF',8)->nullable();
            $table->char('PERSDCANNIF',8)->nullable();
            $table->integer('PERSNACIONA')->nullable();
            $table->char('PERSPASSPORT',20)->nullable();
            $table->char('PERSNDATA',8)->nullable();
            $table->char('PERSPARE',20)->nullable();
            $table->char('PERSMARE',20)->nullable();
            $table->char('PERSSEXE',1)->nullable();
            $table->char('PERSSW',1)->nullable();
            $table->char('IDIOCOD',1)->nullable();
            $table->integer('PERSVNUM')->nullable();
            $table->char('STDAPLADD',5)->nullable();
            $table->char('STDAPLMOD',5)->nullable();
            $table->char('STDUGR',20)->nullable();
            $table->char('STDUMOD',20)->nullable();
            $table->char('STDDGR',8)->nullable();
            $table->char('STDDMOD',8)->nullable();
            $table->char('STDHGR',6)->nullable();
            $table->char('STDHMOD',6)->nullable();
            $table->integer('CONTVNUM')->nullable();
            $table->char('NIFORIG',10)->nullable();
            $table->string('PERSCODOLD',30)->nullable();
            $table->char('VALDATA',8)->nullable();
            $table->char('BAIXASW',1)->nullable();
            $table->string('GUID',32)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
