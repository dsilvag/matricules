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
        Schema::create('instances', function (Blueprint $table) {
            $table->char('RESNUME',11)->primary();
            $table->CHAR('NUMEXP',11)->nullable();
            $table->string('DECRETAT')->nullable();
            $table->string('VALIDAT')->nullable();
            $table->integer('PERSCOD')->nullable();
            $table->foreign('PERSCOD')->references('PERSCOD')->on('people')
                ->onDelete('cascade');
            $table->integer('REPRCOD')->nullable();
            $table->foreign('REPRCOD')->references('PERSCOD')->on('people')
                ->onDelete('cascade');
            $table->integer('DOMCOD')->nullable();
            $table->foreign('DOMCOD')->references('DOMCOD')->on('dwellings')
                ->onDelete('cascade');
            $table->boolean('empadronat_si_ivtm')->default(false);
            $table->boolean('empadronat_no_ivtm')->default(false);
            $table->boolean('noempadronat_viu_barri_vell')->default(false);
            $table->String('noempadronat_viu_barri_vell_text')->nullable();
            $table->boolean('pares_menor_edat')->default(false);
            $table->boolean('familiar_adult_major')->default(false);
            $table->boolean('targeta_aparcament_discapacitat')->default(false);
            $table->boolean('vehicle_comercial')->default(false);
            $table->boolean('client_botiga')->default(false);
            $table->boolean('empresa_serveis')->default(false);
            $table->boolean('empresa_constructora')->default(false);
            $table->boolean('familiar_resident')->default(false);
            $table->boolean('acces_excepcional')->default(false);
            $table->boolean('altres_motius')->default(false);
            $table->String('altres_motius_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instances');
    }
};