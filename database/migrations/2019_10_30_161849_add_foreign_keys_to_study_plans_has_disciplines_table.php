<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToStudyPlansHasDisciplinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('study_plans_has_disciplines', function(Blueprint $table)
		{
			$table->foreign('discipline_periods_id')->references('id')->on('discipline_periods')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('disciplines_id')->references('id')->on('disciplines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('study_plans_id')->references('id')->on('study_plans')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('study_plans_has_disciplines', function(Blueprint $table)
		{
			$table->dropForeign('study_plans_has_disciplines_discipline_periods_id_foreign');
			$table->dropForeign('study_plans_has_disciplines_disciplines_id_foreign');
			$table->dropForeign('study_plans_has_disciplines_study_plans_id_foreign');
		});
	}

}
