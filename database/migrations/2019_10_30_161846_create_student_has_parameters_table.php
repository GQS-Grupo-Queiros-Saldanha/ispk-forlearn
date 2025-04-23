<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentHasParametersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('student_has_parameters', function(Blueprint $table)
		{
			$table->integer('students_id')->unsigned()->nullable()->index('student_has_parameters_students_id')->comment('ID do estudante');
			$table->integer('parameters_id')->unsigned()->nullable()->index('student_has_parameters_parameters_id')->comment('ID do parâmetro');
			$table->integer('enrollments_id')->unsigned()->nullable()->index('student_has_parameters_enrollments_id')->comment('ID da inscrição');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('student_has_parameters');
	}

}
