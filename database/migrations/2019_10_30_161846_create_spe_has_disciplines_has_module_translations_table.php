<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSpeHasDisciplinesHasModuleTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spe_has_disciplines_has_module_translations', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('spe_has_disciplines_has_module_id')->unsigned()->index('spe_has_disciplines_has_module_translations_spe_has_discipline_has_module_id');
			$table->integer('language_id')->unsigned()->index('language_id');
			$table->string('display_name', 191);
			$table->text('description', 16777215);
			$table->boolean('active');
			$table->integer('version');
			$table->timestamps();
			$table->dateTime('deleted_at')->default('0000-00-00 00:00:00');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('spe_has_disciplines_has_module_translations');
	}

}
