<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEnrollmentsHasDisciplinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('enrollments_has_disciplines', function(Blueprint $table)
		{
			$table->foreign('enrollments_id', 'enrollments_has_disciplines_ibfk_1')->references('id')->on('enrollments')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('disciplines_id', 'enrollments_has_disciplines_ibfk_2')->references('id')->on('disciplines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('optional_groups_id', 'enrollments_has_disciplines_ibfk_3')->references('id')->on('optional_groups')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('enrollments_has_disciplines', function(Blueprint $table)
		{
			$table->dropForeign('enrollments_has_disciplines_ibfk_1');
			$table->dropForeign('enrollments_has_disciplines_ibfk_2');
			$table->dropForeign('enrollments_has_disciplines_ibfk_3');
		});
	}

}
