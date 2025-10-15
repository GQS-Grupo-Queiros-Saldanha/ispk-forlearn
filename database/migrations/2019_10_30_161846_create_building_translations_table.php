<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBuildingTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('building_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('building_id')->unsigned()->index('building_translations_building_id');
			$table->integer('language_id')->unsigned()->index('building_translations_language_id');
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
		Schema::drop('building_translations');
	}

}
