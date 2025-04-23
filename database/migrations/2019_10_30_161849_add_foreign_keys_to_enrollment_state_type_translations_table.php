<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEnrollmentStateTypeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('enrollment_state_type_translations', function(Blueprint $table)
		{
			$table->foreign('enrollment_state_types_id', 'enrollment_state_type_translations_ibfk_1')->references('id')->on('enrollment_state_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('language_id', 'enrollment_state_type_translations_ibfk_2')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('enrollment_state_type_translations', function(Blueprint $table)
		{
			$table->dropForeign('enrollment_state_type_translations_ibfk_1');
			$table->dropForeign('enrollment_state_type_translations_ibfk_2');
		});
	}

}
