<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSpeHasAccessTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('spe_has_access_types', function(Blueprint $table)
		{
			$table->foreign('access_type_id', 'spe_has_at_access_types_id_foreign')->references('id')->on('access_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('spe_id', 'spe_has_at_study_plan_editions_id_foreign')->references('id')->on('study_plan_editions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('spe_has_access_types', function(Blueprint $table)
		{
			$table->dropForeign('spe_has_at_access_types_id_foreign');
			$table->dropForeign('spe_has_at_study_plan_editions_id_foreign');
		});
	}

}
