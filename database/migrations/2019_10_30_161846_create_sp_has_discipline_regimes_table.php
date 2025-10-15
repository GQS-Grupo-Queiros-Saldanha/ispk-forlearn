<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSpHasDisciplineRegimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sp_has_discipline_regimes', function(Blueprint $table)
		{
			$table->integer('sp_has_disciplines_id')->unsigned()->index('sp_has_discipline_regimes_sp_has_disciplines_id_foreign');
			$table->integer('discipline_regimes_id')->unsigned()->index('sp_has_discipline_regimes_discipline_regimes_id_foreign');
			$table->float('hours')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sp_has_discipline_regimes');
	}

}
