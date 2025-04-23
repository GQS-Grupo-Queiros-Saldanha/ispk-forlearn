<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToScheduleEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schedule_event', function(Blueprint $table)
		{
			$table->foreign('created_by', 'schedule_events_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('updated_by', 'schedule_events_ibfk_2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('deleted_by', 'schedule_events_ibfk_3')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('schedule_type_time_id', 'schedule_events_ibfk_4')->references('id')->on('schedule_type_times')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('spe_discipline_id', 'schedule_events_ibfk_5')->references('id')->on('spe_has_disciplines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('room_id', 'schedule_events_ibfk_6')->references('id')->on('rooms')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('day_of_the_week_id', 'schedule_events_ibfk_7')->references('id')->on('days_of_the_week')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('schedule_event', function(Blueprint $table)
		{
			$table->dropForeign('schedule_events_ibfk_1');
			$table->dropForeign('schedule_events_ibfk_2');
			$table->dropForeign('schedule_events_ibfk_3');
			$table->dropForeign('schedule_events_ibfk_4');
			$table->dropForeign('schedule_events_ibfk_5');
			$table->dropForeign('schedule_events_ibfk_6');
			$table->dropForeign('schedule_events_ibfk_7');
		});
	}

}
