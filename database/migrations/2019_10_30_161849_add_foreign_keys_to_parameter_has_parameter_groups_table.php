<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToParameterHasParameterGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('parameter_has_parameter_groups', function(Blueprint $table)
		{
			$table->foreign('parameter_group_id')->references('id')->on('parameter_groups')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('parameter_id')->references('id')->on('parameters')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('parameter_has_parameter_groups', function(Blueprint $table)
		{
			$table->dropForeign('parameter_has_parameter_groups_parameter_group_id_foreign');
			$table->dropForeign('parameter_has_parameter_groups_parameter_id_foreign');
		});
	}

}
