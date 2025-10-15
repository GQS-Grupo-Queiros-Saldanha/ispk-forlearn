<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEnrollmentStateTypeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('enrollment_state_type_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('language_id')->unsigned()->index('enrollment_state_type_translations_language_id');
			$table->integer('enrollment_state_types_id')->unsigned()->index('enrollment_state_type_translations_enrollment_state_type_translations_ibfk_1');
			$table->string('display_name', 191)->nullable();
			$table->string('description', 191)->nullable()->default('NULL');
			$table->string('abbreviation', 191)->nullable()->default('NULL');
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
		Schema::drop('enrollment_state_type_translations');
	}

}
