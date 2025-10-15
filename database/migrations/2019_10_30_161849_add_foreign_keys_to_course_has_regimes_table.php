<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCourseHasRegimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('course_has_regimes', function(Blueprint $table)
		{
			$table->foreign('course_id', 'course_has_regimes_ibfk_1')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('course_regime_id', 'course_has_regimes_ibfk_2')->references('id')->on('course_regimes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('course_has_regimes', function(Blueprint $table)
		{
			$table->dropForeign('course_has_regimes_ibfk_1');
			$table->dropForeign('course_has_regimes_ibfk_2');
		});
	}

}
