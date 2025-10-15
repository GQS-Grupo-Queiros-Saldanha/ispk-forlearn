<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAverageCalculationRulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('average_calculation_rules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191)->nullable()->unique();
			$table->integer('created_by')->unsigned()->index('average_calculation_rules_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('average_calculation_rules_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('average_calculation_rules_deleted_by_foreign');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('average_calculation_rules');
	}

}
