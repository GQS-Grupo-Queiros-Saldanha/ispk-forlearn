<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event_options', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('event_id')->unsigned()->index('event_options_event_id');
			$table->string('key', 191);
			$table->string('value', 191);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('event_options');
	}

}
