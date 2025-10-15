<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOptionalGroupsTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('optional_groups_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('optional_groups_id')->unsigned()->index('optional_groups_translations_optional_groups_id_foreign');
			$table->integer('language_id')->unsigned()->index('optional_groups_translations_language_id_foreign');
			$table->string('display_name', 191)->nullable();
			$table->string('description', 191)->nullable();
			$table->integer('version');
			$table->boolean('active')->nullable();
			$table->string('abbreviation', 191)->nullable();
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
		Schema::drop('optional_groups_translations');
	}

}
