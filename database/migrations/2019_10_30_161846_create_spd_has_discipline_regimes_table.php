<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSpdHasDisciplineRegimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spd_has_discipline_regimes', function(Blueprint $table)
		{
			$table->integer('sp_disciplines_id')->unsigned()->index('spd_has_discipline_regimes_sp_disciplines_id_foreign');
			$table->integer('discipline_regimes_id')->unsigned()->index('spd_has_discipline_regimes_discipline_regimes_id_foreign');
			$table->float('hours');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('spd_has_discipline_regimes');
	}

}
