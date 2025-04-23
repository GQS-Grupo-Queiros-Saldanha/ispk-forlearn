<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePrecedencesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('precedences', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('precedence_id')->unsigned()->nullable()->index('precedences_precedence_id_foreign');
			$table->integer('discipline_id')->unsigned()->nullable()->index('precedences_discipline_id_foreign');
			$table->integer('study_plan_editions_id')->unsigned()->index('precedences_study_plan_editions_id_foreign');
			$table->integer('created_by')->unsigned()->index('precedences_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('precedences_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('precedences_deleted_by_foreign');
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
		Schema::drop('precedences');
	}

}
