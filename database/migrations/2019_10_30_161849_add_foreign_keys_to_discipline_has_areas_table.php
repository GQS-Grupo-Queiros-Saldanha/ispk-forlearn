<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDisciplineHasAreasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('discipline_has_areas', function(Blueprint $table)
		{
			$table->foreign('discipline_id', 'discipline_has_areas_ibfk_1')->references('id')->on('disciplines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('discipline_area_id', 'discipline_has_areas_ibfk_2')->references('id')->on('discipline_areas')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('discipline_has_areas', function(Blueprint $table)
		{
			$table->dropForeign('discipline_has_areas_ibfk_1');
			$table->dropForeign('discipline_has_areas_ibfk_2');
		});
	}

}
