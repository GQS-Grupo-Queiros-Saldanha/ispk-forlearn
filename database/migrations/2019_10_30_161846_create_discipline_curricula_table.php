<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDisciplineCurriculaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discipline_curricula', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('disciplines_id')->unsigned()->index('discipline_curricula_disciplines_id_foreign');
			$table->integer('study_plan_editions_id')->unsigned()->index('discipline_curricula_study_plan_editions_id_foreign');
			$table->integer('created_by')->unsigned()->index('discipline_curricula_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('discipline_curricula_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('discipline_curricula_deleted_by_foreign');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('discipline_curricula');
	}

}
