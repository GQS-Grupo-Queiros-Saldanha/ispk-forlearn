<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudyPlansHasOptionalGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('study_plans_has_optional_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('study_plans_id')->unsigned()->index('study_plans_has_optional_groups_study_plans_id_foreign');
			$table->integer('optional_groups_id')->unsigned()->index('study_plans_has_optional_groups_optional_groups_id_foreign');
			$table->integer('discipline_periods_id')->unsigned()->index('study_plans_has_optional_groups_discipline_periods_id_foreign');
			$table->integer('year');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('study_plans_has_optional_groups');
	}

}
