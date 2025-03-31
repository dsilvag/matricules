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
/*

    public function up(): void
    {
        Schema::create('instances', function (Blueprint $table) {
            $table->char('RESNUME', 11)->primary();
            $table->char('RESDREG', 8);
            $table->char('RESHREG', 6);
            $table->char('ASSUMCOD', 4)->nullable();
            $table->char('AREACODDES', 8)->nullable();
            $table->char('DEPCODDES', 4)->nullable();
            $table->integer('PERSCOD');
            $table->foreign('PERSCOD')->references('PERSCOD')->on('people')->onDelete('cascade');
            $table->integer('PERSND')->nullable();
            $table->integer('REPRCOD')->nullable();
            $table->integer('REPRND')->nullable();
            $table->integer('RESORGORG')->nullable();
            $table->string('RESORGORGNUM', 20)->nullable();
            $table->integer('RESORGORGNDOM')->nullable();
            $table->char('RESDORGORG', 8)->nullable();
            $table->char('RESHORGORG', 6)->nullable();
            $table->integer('DOMCOD')->nullable();
            $table->char('TRANSTIP', 4)->nullable();
            $table->string('RESTRANSNUM', 255)->nullable();
            $table->char('TRANSDATA', 8)->nullable();
            $table->string('TRANSOBS', 255)->nullable();
            $table->char('EXTRCOD', 4)->nullable();
            $table->string('RESTEXT', 4000)->nullable();
            $table->integer('RESNPAG')->nullable();
            $table->string('ARXCOD', 20)->nullable();
            $table->string('ARXSIGTOP', 40)->nullable();
            $table->char('IDIOMACOD', 1)->nullable();
            $table->char('RESRELE', 11)->nullable();
            $table->char('AREACOD', 8)->nullable();
            $table->char('DEPCOD', 4)->nullable();
            $table->char('RESDPRE', 8)->nullable();
            $table->char('RESHPRE', 6)->nullable();
            $table->char('RESDDOC', 8)->nullable();
            $table->integer('CONNUM')->nullable();
            $table->char('STDUGR', 20)->nullable();
            $table->char('STDUMOD', 20)->nullable();
            $table->char('STDDGR', 8)->nullable();
            $table->char('STDHGR', 6)->nullable();
            $table->char('STDDMOD', 8)->nullable();
            $table->char('STDHMOD', 6)->nullable();
            $table->string('PERSNOM', 255)->nullable();
            $table->string('PERSCOG1', 25)->nullable();
            $table->string('PERSCOG2', 25)->nullable();
            $table->string('DOMICILI', 255)->nullable();
            $table->string('REPNOM', 255)->nullable();
            $table->string('REPCOG1', 25)->nullable();
            $table->string('REPCOG2', 25)->nullable();
            $table->string('REPDOMI', 255)->nullable();
            $table->char('PERSNIF', 20)->nullable();
            $table->char('REPNIF', 20)->nullable();
            $table->integer('FCONTACN')->nullable();
            $table->integer('NUMCONORDRE')->nullable();
            $table->string('AREADESC', 60)->nullable();
            $table->string('AREADESCRESP', 60)->nullable();
            $table->string('DEPDESC', 60)->nullable();
            $table->string('DEPDESCRESP', 60)->nullable();
            $table->string('DESCASSUMPTE', 255)->nullable();
            $table->string('OBSERVACIONS', 4000)->nullable();
            $table->char('ASSUMCODORG', 4)->nullable();
            $table->char('ENTCOD', 5);
            $table->char('RESNUM', 11);
            $table->char('RESNUMEEXT', 11)->nullable();
            $table->char('PLATAFORMA', 4)->nullable();
            $table->string('EFACT_OBS', 1024)->nullable();
            $table->char('EFACT_COMPTABILITAT', 8)->nullable();
            $table->char('EFACT_ESTAT', 12)->nullable();
            $table->string('EFACT_NUMFACTURA', 20)->nullable();
            $table->string('EFACT_PROVEIDOR', 30)->nullable();
            $table->char('PERSPAR1', 6)->nullable();
            $table->char('PERSPAR2', 6)->nullable();
            $table->char('REPPAR1', 6)->nullable();
            $table->char('REPPAR2', 6)->nullable();
            $table->string('RESPONSABLE', 255)->nullable();
            $table->char('ORG_ORIGEN_DESTI', 1)->nullable();
            $table->char('MUXAOC_ANOTACIO', 20)->nullable();
            $table->integer('RESORGCONTAC')->nullable();
            $table->string('PERSCONTACN', 255)->nullable();
            $table->string('PERSNUMCONORDRE', 255)->nullable();
            $table->integer('REPCONTAC')->nullable();
            $table->integer('REPFCONTAC')->nullable();
            $table->string('REPCONTACDESC', 255)->nullable();
            $table->string('REPFCONTACDESC', 255)->nullable();
            $table->integer('RESORGFCONTAC')->nullable();
            $table->char('PERSNOT', 1)->nullable();
            $table->char('DOMNOT', 1)->nullable();
            $table->integer('SWAREADEPKO')->nullable();
            $table->string('RESNUMEAOC', 30)->nullable();
            $table->integer('SW_MUX')->nullable();
            $table->string('AREADEPKODESC', 1024)->nullable();
            $table->integer('FCONTACTSMS')->nullable();
            $table->integer('FCONTACTMAIL')->nullable();
            $table->string('NOTSMS', 255)->nullable();
            $table->string('NOTMAIL', 255)->nullable();
            $table->integer('SWTRACTAT')->nullable();
            $table->integer('PERSNDMAIL')->nullable();
            $table->integer('NUMCONORDREMAIL')->nullable();
            $table->integer('PERSNDSMS')->nullable();
            $table->integer('NUMCONORDRESMS')->nullable();
            $table->char('FACE_NUMERO_ENTRADA', 254)->nullable();
            $table->char('FACE_ESTAT_CONFIRM', 254)->nullable();
            $table->char('MUXAOC_ESTAT', 5)->nullable();
            $table->char('MUXAOC_ASSENT', 20)->nullable();
            $table->char('MUXAOC_DATAALTA', 40)->nullable();
            $table->string('MUXAOC_MSGERROR', 2000)->nullable();
            $table->char('EFACT_FACCODI_ORDRE', 10)->nullable();
            $table->decimal('EFACT_IMPORT', 12, 2)->nullable();
            $table->string('ETRAM_IDTRAMIT', 32)->nullable();
            $table->string('ETRAM_CODI_TIPUSTRAMIT', 9)->nullable();
            $table->string('ETRAM_DESC_TIPUSTRAMIT', 255)->nullable();
            $table->string('ETRAM_SMS', 13)->nullable();
            $table->string('ETRAM_MAIL', 40)->nullable();
            $table->string('ETRAM_NUMREGENT', 50)->nullable();
            $table->string('ETRAM_NUMEXPED', 50)->nullable();
            $table->integer('TRAMCOD')->nullable();
            $table->string('TRAMDESC', 1024)->nullable();
            $table->string('MUX_IDAPLICACIO', 10)->nullable();
            $table->integer('TRAMITCOD')->nullable();
            $table->string('TRAMITDESC', 1024)->nullable();
            $table->string('POBLDESC_INT', 255)->nullable();
            $table->string('POBLDESC_REPR', 255)->nullable();
            $table->string('MUX_DESCTRAMIT', 1024)->nullable();
            $table->string('MUX_IDTRANSACCIO', 70)->nullable();
            $table->char('TRANSHORA', 6)->nullable();
            $table->string('INE10_ORIGEN', 10)->nullable();
            $table->integer('FUE')->nullable();
            $table->char('UNITATDESTICOD', 9)->nullable();
            $table->char('OFICINADESTICOD', 9)->nullable();
            $table->char('OFICINAORIGENCOD', 9)->nullable();
            $table->char('UNITATORIGENCOD', 9)->nullable();
            $table->string('EXPOSO', 4000)->nullable();
            $table->string('SOLICITO', 4000)->nullable();
            $table->string('VERSIO_MUX', 10)->nullable();
        });
    }

 */
