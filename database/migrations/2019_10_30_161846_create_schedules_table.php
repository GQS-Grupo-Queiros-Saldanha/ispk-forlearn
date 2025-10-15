<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSchedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('schedules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('spe_id')->unsigned()->index('schedules_spe_id');
			$table->integer('schedule_type_id')->unsigned()->index('schedules_schedule_type_id');
			$table->integer('discipline_class_id')->unsigned()->index('schedules_discipline_class_id');
			$table->string('code', 191)->unique();
			$table->integer('created_by')->unsigned()->index('schedules_created_by');
			$table->integer('updated_by')->unsigned()->index('schedules_updated_by');
			$table->integer('deleted_by')->unsigned()->index('schedules_deleted_by');
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
		Schema::drop('schedules');
	}

}
