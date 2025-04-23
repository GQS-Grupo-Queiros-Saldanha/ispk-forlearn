<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScheduleTypeTimeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('schedule_type_time_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('schedule_type_time_id')->unsigned()->index('schedule_type_time_translations_schedule_type_time_id');
			$table->integer('language_id')->unsigned()->index('schedule_type_time_translations_language_id');
			$table->string('display_name', 191);
			$table->string('description', 191);
			$table->integer('version');
			$table->boolean('active');
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
		Schema::drop('schedule_type_time_translations');
	}

}
