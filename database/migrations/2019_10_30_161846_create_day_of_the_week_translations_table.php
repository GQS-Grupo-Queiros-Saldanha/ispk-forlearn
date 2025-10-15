<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDayOfTheWeekTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('day_of_the_week_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('day_of_the_week_id')->unsigned()->index('day_of_the_week_translations_days_of_the_week_id');
			$table->integer('language_id')->unsigned()->index('day_of_the_week_translations_language_id');
			$table->string('display_name', 191);
			$table->string('description', 191);
			$table->string('abbreviation', 191);
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
		Schema::drop('day_of_the_week_translations');
	}

}
