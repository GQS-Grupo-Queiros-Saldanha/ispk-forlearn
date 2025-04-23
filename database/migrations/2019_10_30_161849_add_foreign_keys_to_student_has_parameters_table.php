<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToStudentHasParametersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('student_has_parameters', function(Blueprint $table)
		{
			$table->foreign('students_id', 'student_has_parameters_ibfk_1')->references('id')->on('students')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('parameters_id', 'student_has_parameters_ibfk_2')->references('id')->on('parameters')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('enrollments_id', 'student_has_parameters_ibfk_3')->references('id')->on('enrollments')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('student_has_parameters', function(Blueprint $table)
		{
			$table->dropForeign('student_has_parameters_ibfk_1');
			$table->dropForeign('student_has_parameters_ibfk_2');
			$table->dropForeign('student_has_parameters_ibfk_3');
		});
	}

}
