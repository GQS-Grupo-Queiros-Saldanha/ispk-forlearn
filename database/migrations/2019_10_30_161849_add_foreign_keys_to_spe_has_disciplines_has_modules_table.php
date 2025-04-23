<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSpeHasDisciplinesHasModulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('spe_has_disciplines_has_modules', function(Blueprint $table)
		{
			$table->foreign('spe_has_disciplines_id', 'spe_has_disciplines_has_modules_ibfk_1')->references('id')->on('spe_has_disciplines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('spe_has_disciplines_has_modules', function(Blueprint $table)
		{
			$table->dropForeign('spe_has_disciplines_has_modules_ibfk_1');
		});
	}

}
