<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSpeHasOptionalGroupsHasDisciplinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('spe_has_optional_groups_has_disciplines', function(Blueprint $table)
		{
			$table->foreign('disciplines_id')->references('id')->on('disciplines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('spe_has_og_id')->references('id')->on('spe_has_optional_groups')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('spe_has_optional_groups_has_disciplines', function(Blueprint $table)
		{
			$table->dropForeign('spe_has_optional_groups_has_disciplines_disciplines_id_foreign');
			$table->dropForeign('spe_has_optional_groups_has_disciplines_spe_has_og_id_foreign');
		});
	}

}
