<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSpeHasOptionalGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('spe_has_optional_groups', function(Blueprint $table)
		{
			$table->foreign('optional_groups_id')->references('id')->on('optional_groups')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('period_types_id')->references('id')->on('period_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('spe_id')->references('id')->on('study_plan_editions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('spe_has_optional_groups', function(Blueprint $table)
		{
			$table->dropForeign('spe_has_optional_groups_optional_groups_id_foreign');
			$table->dropForeign('spe_has_optional_groups_period_types_id_foreign');
			$table->dropForeign('spe_has_optional_groups_spe_id_foreign');
		});
	}

}
