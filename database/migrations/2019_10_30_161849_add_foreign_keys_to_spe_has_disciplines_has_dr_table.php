<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSpeHasDisciplinesHasDrTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('spe_has_disciplines_has_dr', function(Blueprint $table)
		{
			$table->foreign('discipline_regimes_id')->references('id')->on('discipline_regimes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('spe_has_disciplines_id')->references('id')->on('spe_has_disciplines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('spe_has_disciplines_has_dr', function(Blueprint $table)
		{
			$table->dropForeign('spe_has_disciplines_has_dr_discipline_regimes_id_foreign');
			$table->dropForeign('spe_has_disciplines_has_dr_spe_has_disciplines_id_foreign');
		});
	}

}
