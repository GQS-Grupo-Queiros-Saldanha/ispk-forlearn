<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseRegimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_regimes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191);
			$table->integer('created_by')->unsigned()->index('course_regimes_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('course_regimes_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('course_regimes_deleted_by_foreign');
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
		Schema::drop('course_regimes');
	}

}
