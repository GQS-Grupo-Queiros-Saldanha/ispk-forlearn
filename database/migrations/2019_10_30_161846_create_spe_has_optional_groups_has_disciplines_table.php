<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSpeHasOptionalGroupsHasDisciplinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spe_has_optional_groups_has_disciplines', function(Blueprint $table)
		{
			$table->integer('spe_has_og_id')->unsigned()->index('spe_has_optional_groups_has_disciplines_spe_has_og_id_foreign');
			$table->integer('disciplines_id')->unsigned()->index('spe_has_optional_groups_has_disciplines_disciplines_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('spe_has_optional_groups_has_disciplines');
	}

}
