<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSpeHasOptionalGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spe_has_optional_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('spe_id')->unsigned()->index('spe_has_optional_groups_spe_id_foreign');
			$table->integer('optional_groups_id')->unsigned()->index('spe_has_optional_groups_optional_groups_id_foreign');
			$table->integer('period_types_id')->unsigned()->index('spe_has_optional_groups_period_types_id_foreign');
			$table->integer('year');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('spe_has_optional_groups');
	}

}
