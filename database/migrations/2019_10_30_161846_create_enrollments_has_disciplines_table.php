<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEnrollmentsHasDisciplinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('enrollments_has_disciplines', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('enrollments_id')->unsigned()->index('enrollments_has_disciplines_enrollments_id');
			$table->integer('disciplines_id')->unsigned()->index('enrollments_has_disciplines_disciplines_id');
			$table->integer('optional_groups_id')->unsigned()->nullable()->index('enrollments_has_disciplines_optional_groups_id');
			$table->integer('status');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('enrollments_has_disciplines');
	}

}
