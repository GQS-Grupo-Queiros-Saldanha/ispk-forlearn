<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('event_type_id')->unsigned()->index('events_event_type_id');
			$table->integer('created_by')->unsigned()->index('events_created_by');
			$table->integer('updated_by')->unsigned()->nullable()->index('events_updated_by');
			$table->integer('deleted_by')->unsigned()->nullable()->index('events_deleted_by');
			$table->timestamp('start')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->dateTime('end')->default('0000-00-00 00:00:00');
			$table->boolean('all_day');
			$table->string('url', 191)->nullable();
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
		Schema::drop('events');
	}

}
