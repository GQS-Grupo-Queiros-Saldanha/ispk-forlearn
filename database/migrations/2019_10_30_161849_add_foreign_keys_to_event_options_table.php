<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEventOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('event_options', function(Blueprint $table)
		{
			$table->foreign('event_id', 'event_options_ibfk_1')->references('id')->on('events')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('event_options', function(Blueprint $table)
		{
			$table->dropForeign('event_options_ibfk_1');
		});
	}

}
