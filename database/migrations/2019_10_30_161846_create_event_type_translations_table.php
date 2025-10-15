<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventTypeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event_type_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('event_type_id')->unsigned()->index('event_type_translations_event_type_id');
			$table->integer('language_id')->unsigned()->index('event_type_translations_language_id');
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
		Schema::drop('event_type_translations');
	}

}
