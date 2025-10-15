<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateParameterTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('parameter_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('parameters_id')->unsigned()->index('parameter_translations_parameters_id_foreign');
			$table->integer('language_id')->unsigned()->index('parameter_translations_language_id_foreign');
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
		Schema::drop('parameter_translations');
	}

}
