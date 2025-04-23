<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSpeHasDisciplinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('spe_has_disciplines', function(Blueprint $table)
		{
			$table->foreign('disciplines_id')->references('id')->on('disciplines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('period_types_id')->references('id')->on('period_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('study_plan_editions_id')->references('id')->on('study_plan_editions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('spe_has_disciplines', function(Blueprint $table)
		{
			$table->dropForeign('spe_has_disciplines_disciplines_id_foreign');
			$table->dropForeign('spe_has_disciplines_period_types_id_foreign');
			$table->dropForeign('spe_has_disciplines_study_plan_editions_id_foreign');
		});
	}

}
