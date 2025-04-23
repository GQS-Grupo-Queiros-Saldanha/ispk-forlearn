<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEnrollmentsHasEnrollmentStateTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('enrollments_has_enrollment_state_types', function(Blueprint $table)
		{
			$table->foreign('enrollments_id', 'enrollments_has_enrollment_state_types_ibfk_1')->references('id')->on('enrollments')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('enrollment_state_types_id', 'enrollments_has_enrollment_state_types_ibfk_2')->references('id')->on('enrollment_state_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('created_by', 'enrollments_has_enrollment_state_types_ibfk_3')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('updated_by', 'enrollments_has_enrollment_state_types_ibfk_4')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('enrollments_has_enrollment_state_types', function(Blueprint $table)
		{
			$table->dropForeign('enrollments_has_enrollment_state_types_ibfk_1');
			$table->dropForeign('enrollments_has_enrollment_state_types_ibfk_2');
			$table->dropForeign('enrollments_has_enrollment_state_types_ibfk_3');
			$table->dropForeign('enrollments_has_enrollment_state_types_ibfk_4');
		});
	}

}
