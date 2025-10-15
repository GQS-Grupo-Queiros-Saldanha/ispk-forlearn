<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDisciplineClassesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('discipline_classes', function(Blueprint $table)
		{
			$table->foreign('classes_id')->references('id')->on('classes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('discipline_regimes_id')->references('id')->on('discipline_regimes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('disciplines_id')->references('id')->on('disciplines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('study_plan_editions_id')->references('id')->on('study_plan_editions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('discipline_classes', function(Blueprint $table)
		{
			$table->dropForeign('discipline_classes_classes_id_foreign');
			$table->dropForeign('discipline_classes_discipline_regimes_id_foreign');
			$table->dropForeign('discipline_classes_disciplines_id_foreign');
			$table->dropForeign('discipline_classes_study_plan_editions_id_foreign');
		});
	}

}
