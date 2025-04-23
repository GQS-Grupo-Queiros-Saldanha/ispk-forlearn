<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScheduleTypeTimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('schedule_type_times', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('schedule_type_id')->unsigned()->index('schedule_type_times_schedule_type_id');
			$table->string('code', 191)->unique();
			$table->time('start');
			$table->time('end');
			$table->integer('order')->default(0);
			$table->integer('created_by')->unsigned()->index('schedule_type_times_created_by');
			$table->integer('updated_by')->unsigned()->nullable()->index('schedule_type_times_updated_by');
			$table->integer('deleted_by')->unsigned()->nullable()->index('schedule_type_times_deleted_by');
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
		Schema::drop('schedule_type_times');
	}

}
