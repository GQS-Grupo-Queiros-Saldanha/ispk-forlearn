<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePeriodTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('period_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('discipline_periods_id')->unsigned()->index('period_types_discipline_periods_id_foreign');
			$table->string('code', 191)->nullable()->unique();
			$table->integer('created_by')->unsigned()->index('period_types_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('period_types_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('period_types_deleted_by_foreign');
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
		Schema::drop('period_types');
	}

}
