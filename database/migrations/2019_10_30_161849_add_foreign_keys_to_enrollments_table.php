<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEnrollmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('enrollments', function(Blueprint $table)
		{
			$table->foreign('students_id', 'enrollments_ibfk_1')->references('id')->on('students')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('study_plan_editions_id', 'enrollments_ibfk_2')->references('id')->on('study_plan_editions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('access_type_id', 'enrollments_ibfk_3')->references('id')->on('access_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('created_by', 'enrollments_ibfk_4')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('updated_by', 'enrollments_ibfk_5')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('enrollments', function(Blueprint $table)
		{
			$table->dropForeign('enrollments_ibfk_1');
			$table->dropForeign('enrollments_ibfk_2');
			$table->dropForeign('enrollments_ibfk_3');
			$table->dropForeign('enrollments_ibfk_4');
			$table->dropForeign('enrollments_ibfk_5');
		});
	}

}
