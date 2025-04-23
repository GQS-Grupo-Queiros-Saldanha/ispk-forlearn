<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateParameterHasParameterGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('parameter_has_parameter_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('parameter_id')->unsigned()->index('parameter_has_parameter_groups_parameter_id_foreign');
			$table->integer('parameter_group_id')->unsigned()->index('parameter_has_parameter_groups_parameter_group_id_foreign');
			$table->timestamps();
			$table->integer('order')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('parameter_has_parameter_groups');
	}

}
