<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSpeHasDisciplinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spe_has_disciplines', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('study_plan_editions_id')->unsigned()->index('spe_has_disciplines_study_plan_editions_id_foreign');
			$table->integer('disciplines_id')->unsigned()->index('spe_has_disciplines_disciplines_id_foreign');
			$table->integer('period_types_id')->unsigned()->index('spe_has_disciplines_period_types_id_foreign');
			$table->integer('year');
			$table->boolean('optional')->nullable();
			$table->float('total_hours')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('spe_has_disciplines');
	}

}
