<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDisciplineClassesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discipline_classes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('classes_id')->unsigned()->index('discipline_classes_classes_id_foreign');
			$table->string('display_name', 191);
			$table->integer('disciplines_id')->unsigned()->index('discipline_classes_disciplines_id_foreign');
			$table->integer('study_plan_editions_id')->unsigned()->index('discipline_classes_study_plan_editions_id_foreign');
			$table->integer('discipline_regimes_id')->unsigned()->index('discipline_classes_discipline_regimes_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('discipline_classes');
	}

}
