<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToParameterOptionHasParametersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('parameter_option_has_parameters', function(Blueprint $table)
		{
			$table->foreign('parameters_id', 'parameter_option_has_parameters_ibfk_1')->references('id')->on('parameters')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('parameter_option_id', 'parameter_option_has_parameters_ibfk_2')->references('id')->on('parameter_options')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('parameter_option_has_parameters', function(Blueprint $table)
		{
			$table->dropForeign('parameter_option_has_parameters_ibfk_1');
			$table->dropForeign('parameter_option_has_parameters_ibfk_2');
		});
	}

}
