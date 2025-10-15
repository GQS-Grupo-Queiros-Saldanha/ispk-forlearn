<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToScheduleTypeTimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schedule_type_times', function(Blueprint $table)
		{
			$table->foreign('created_by', 'schedule_type_times_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('updated_by', 'schedule_type_times_ibfk_2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('deleted_by', 'schedule_type_times_ibfk_3')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('schedule_type_id', 'schedule_type_times_ibfk_4')->references('id')->on('schedule_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('schedule_type_times', function(Blueprint $table)
		{
			$table->dropForeign('schedule_type_times_ibfk_1');
			$table->dropForeign('schedule_type_times_ibfk_2');
			$table->dropForeign('schedule_type_times_ibfk_3');
			$table->dropForeign('schedule_type_times_ibfk_4');
		});
	}

}
