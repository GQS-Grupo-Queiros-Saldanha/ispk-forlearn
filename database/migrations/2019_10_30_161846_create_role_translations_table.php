<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRoleTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('role_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('role_id')->unsigned()->index('role_translations_role_id_foreign');
			$table->integer('language_id')->unsigned()->index('role_translations_language_id_foreign');
			$table->string('display_name', 191)->nullable();
			$table->string('description', 191)->nullable();
			$table->integer('version')->nullable();
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
		Schema::drop('role_translations');
	}

}
