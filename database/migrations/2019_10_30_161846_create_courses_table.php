<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoursesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('courses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191);
			$table->integer('duration_value')->unsigned();
			$table->integer('departments_id')->unsigned()->index('courses_departments_id_foreign');
			$table->integer('course_cycles_id')->unsigned()->index('courses_course_cycles_id_foreign');
			$table->integer('degrees_id')->unsigned()->index('courses_degrees_id_foreign');
			$table->integer('duration_types_id')->unsigned()->index('courses_duration_types_id_foreign');
			$table->integer('created_by')->unsigned()->index('courses_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('courses_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('courses_deleted_by_foreign');
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
		Schema::drop('courses');
	}

}
