<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudyPlansHasDisciplinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('study_plans_has_disciplines', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('study_plans_id')->unsigned()->index('study_plans_has_disciplines_study_plans_id_foreign');
			$table->integer('disciplines_id')->unsigned()->index('study_plans_has_disciplines_disciplines_id_foreign');
			$table->integer('discipline_periods_id')->unsigned()->index('study_plans_has_disciplines_discipline_periods_id_foreign');
			$table->float('total_hours');
			$table->integer('years');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('study_plans_has_disciplines');
	}

}
