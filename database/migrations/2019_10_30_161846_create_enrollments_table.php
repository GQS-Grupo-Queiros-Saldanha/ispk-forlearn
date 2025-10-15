<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEnrollmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('enrollments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('students_id')->unsigned()->index('enrollments_students_id');
			$table->integer('candidate_id')->unsigned()->nullable();
			$table->integer('study_plan_editions_id')->unsigned()->index('enrollments_study_plan_editions_id');
			$table->integer('access_type_id')->unsigned()->index('enrollments_access_type_id');
			$table->boolean('status');
			$table->smallInteger('year')->nullable();
			$table->boolean('partial_time');
			$table->integer('created_by')->unsigned()->index('enrollments_created_by');
			$table->integer('updated_by')->unsigned()->nullable()->index('enrollments_updated_by');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('enrollments');
	}

}
