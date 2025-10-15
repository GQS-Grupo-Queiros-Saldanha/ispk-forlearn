<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDisciplineAbsenceConfigurationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discipline_absence_configuration', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('study_plan_editions_id')->unsigned()->index('discipline_absence_configuration_study_plan_editions_id_foreign');
			$table->integer('discipline_regimes_id')->unsigned()->nullable();
			$table->integer('disciplines_id')->unsigned()->index('discipline_absence_configuration_disciplines_id_foreign');
			$table->integer('max_absences')->nullable();
			$table->boolean('is_total')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('discipline_absence_configuration');
	}

}
