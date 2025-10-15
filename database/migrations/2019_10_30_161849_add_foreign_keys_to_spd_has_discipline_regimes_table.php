<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSpdHasDisciplineRegimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('spd_has_discipline_regimes', function(Blueprint $table)
		{
			$table->foreign('discipline_regimes_id')->references('id')->on('discipline_regimes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('sp_disciplines_id')->references('id')->on('study_plans_has_disciplines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('spd_has_discipline_regimes', function(Blueprint $table)
		{
			$table->dropForeign('spd_has_discipline_regimes_discipline_regimes_id_foreign');
			$table->dropForeign('spd_has_discipline_regimes_sp_disciplines_id_foreign');
		});
	}

}
