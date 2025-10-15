<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDisciplineAbsenceConfigurationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('discipline_absence_configuration', function(Blueprint $table)
		{
			$table->foreign('disciplines_id')->references('id')->on('disciplines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
		Schema::table('discipline_absence_configuration', function(Blueprint $table)
		{
			$table->dropForeign('discipline_absence_configuration_disciplines_id_foreign');
			$table->dropForeign('discipline_absence_configuration_study_plan_editions_id_foreign');
		});
	}

}
