<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSpeHasDisciplinesHasModulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spe_has_disciplines_has_modules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('spe_has_disciplines_id')->unsigned()->index('spe_has_disciplines_has_modules_spe_has_disciplines_id');
			$table->integer('created_by')->unsigned();
			$table->integer('updated_by')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('spe_has_disciplines_has_modules');
	}

}
