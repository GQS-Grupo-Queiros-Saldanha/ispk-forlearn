<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDegreeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('degree_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('language_id')->unsigned()->index('degree_translations_language_id_foreign');
			$table->integer('degrees_id')->unsigned()->index('degree_translations_degrees_id_foreign');
			$table->string('display_name', 191)->nullable();
			$table->string('description', 191)->nullable();
			$table->integer('version');
			$table->boolean('active')->nullable();
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
		Schema::drop('degree_translations');
	}

}
