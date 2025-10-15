<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToStudyPlanEditionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('study_plan_editions', function(Blueprint $table)
		{
			$table->foreign('average_calculation_rules_id')->references('id')->on('average_calculation_rules')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('lective_years_id')->references('id')->on('lective_years')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('study_plans_id')->references('id')->on('study_plans')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('year_transition_rules_id')->references('id')->on('year_transition_rules')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('study_plan_editions', function(Blueprint $table)
		{
			$table->dropForeign('study_plan_editions_average_calculation_rules_id_foreign');
			$table->dropForeign('study_plan_editions_lective_years_id_foreign');
			$table->dropForeign('study_plan_editions_study_plans_id_foreign');
			$table->dropForeign('study_plan_editions_year_transition_rules_id_foreign');
		});
	}

}
