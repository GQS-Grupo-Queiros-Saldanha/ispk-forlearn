<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSpeHasDisciplinesHasDrTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spe_has_disciplines_has_dr', function(Blueprint $table)
		{
			$table->integer('spe_has_disciplines_id')->unsigned()->index('spe_has_disciplines_has_dr_spe_has_disciplines_id_foreign');
			$table->integer('discipline_regimes_id')->unsigned()->index('spe_has_disciplines_has_dr_discipline_regimes_id_foreign');
			$table->float('hours')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('spe_has_disciplines_has_dr');
	}

}
