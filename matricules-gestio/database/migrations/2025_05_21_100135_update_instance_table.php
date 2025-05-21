<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
	{
		if (Schema::hasTable('instances')) {
			Schema::table('instances', function (Blueprint $table) {
				$table->integer('domicili_acces2')->nullable();
                 $table->foreign('domicili_acces2')->references('DOMCOD')->on('dwellings')
                    ->onDelete('cascade');
				$table->integer('domicili_acces3')->nullable();
                 $table->foreign('domicili_acces3')->references('DOMCOD')->on('dwellings')
                ->onDelete('cascade');
			});
		}
	}
};
