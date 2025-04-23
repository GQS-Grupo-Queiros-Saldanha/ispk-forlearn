<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDisciplineHasAreasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discipline_has_areas', function(Blueprint $table)
		{
			$table->integer('discipline_id')->unsigned()->index('discipline_has_areas_discipline_id');
			$table->integer('discipline_area_id')->unsigned()->index('discipline_has_areas_discipline_area_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('discipline_has_areas');
	}

}
