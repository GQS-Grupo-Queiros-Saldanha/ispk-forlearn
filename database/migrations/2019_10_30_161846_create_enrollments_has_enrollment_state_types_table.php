<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEnrollmentsHasEnrollmentStateTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('enrollments_has_enrollment_state_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('enrollments_id')->unsigned()->index('enrollments_has_enrollment_state_types_enrollments_id');
			$table->integer('enrollment_state_types_id')->unsigned()->index('enrollments_has_enrollment_state_types_enrollment_state_types_id');
			$table->text('explanation');
			$table->integer('created_by')->unsigned()->index('enrollments_has_enrollment_state_types_created_by');
			$table->integer('updated_by')->unsigned()->nullable()->index('enrollments_has_enrollment_state_types_updated_by');
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
		Schema::drop('enrollments_has_enrollment_state_types');
	}

}
