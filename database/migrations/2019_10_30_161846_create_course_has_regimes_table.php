<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseHasRegimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_has_regimes', function(Blueprint $table)
		{
			$table->integer('course_id')->unsigned()->index('course_has_regimes_course_id');
			$table->integer('course_regime_id')->unsigned()->index('course_has_regimes_course_regime_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('course_has_regimes');
	}

}
