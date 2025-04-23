<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScheduleEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('schedule_event', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('schedule_type_time_id')->unsigned()->index('schedule_event_schedule_type_time_id');
			$table->integer('spe_discipline_id')->unsigned()->index('schedule_event_spe_discipline_id');
			$table->integer('room_id')->unsigned()->index('schedule_event_room_id');
			$table->integer('day_of_the_week_id')->unsigned()->index('schedule_event_day_of_the_week_id');
			$table->integer('created_by')->unsigned()->index('schedule_event_created_by');
			$table->integer('updated_by')->unsigned()->nullable()->index('schedule_event_updated_by');
			$table->integer('deleted_by')->unsigned()->nullable()->index('schedule_event_deleted_by');
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
		Schema::drop('schedule_event');
	}

}
