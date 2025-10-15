<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudyPlanEditionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('study_plan_editions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('study_plans_id')->unsigned()->index('study_plan_editions_study_plans_id_foreign');
			$table->integer('lective_years_id')->unsigned()->index('study_plan_editions_lective_years_id_foreign');
			$table->integer('year_transition_rules_id')->unsigned()->index('study_plan_editions_year_transition_rules_id_foreign');
			$table->integer('average_calculation_rules_id')->unsigned()->index('study_plan_editions_average_calculation_rules_id_foreign');
			$table->date('start_date');
			$table->date('end_date');
			$table->boolean('block_enrollments')->nullable();
			$table->integer('max_enrollments')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('study_plan_editions');
	}

}
