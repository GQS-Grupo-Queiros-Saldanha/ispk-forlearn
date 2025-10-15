<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateParameterOptionHasParametersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('parameter_option_has_parameters', function(Blueprint $table)
		{
			$table->integer('parameter_option_id')->unsigned()->nullable()->index('parameter_option_has_parameters_parameter_option_id');
			$table->integer('parameters_id')->unsigned()->nullable()->index('parameter_option_has_parameters_parameters_id');
			$table->integer('order')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('parameter_option_has_parameters');
	}

}
