<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('event_id')->unsigned()->index('event_translations_event_id');
			$table->integer('language_id')->unsigned()->index('event_translations_language_id');
			$table->string('display_name', 191);
			$table->string('description', 191);
			$table->boolean('active');
			$table->integer('version');
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
		Schema::drop('event_translations');
	}

}
