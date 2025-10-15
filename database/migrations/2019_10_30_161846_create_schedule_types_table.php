<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScheduleTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('schedule_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191)->unique();
			$table->integer('created_by')->unsigned()->index('schedule_types_created_by');
			$table->integer('updated_by')->unsigned()->nullable()->index('schedule_types_updated_by');
			$table->integer('deleted_by')->unsigned()->nullable()->index('schedule_types_deleted_by');
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
		Schema::drop('schedule_types');
	}

}
