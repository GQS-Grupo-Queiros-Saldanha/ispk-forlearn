<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAverageCalculationRuleTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('average_calculation_rule_translations', function(Blueprint $table)
		{
			$table->foreign('acr_id')->references('id')->on('average_calculation_rules')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('language_id')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('average_calculation_rule_translations', function(Blueprint $table)
		{
			$table->dropForeign('average_calculation_rule_translations_acr_id_foreign');
			$table->dropForeign('average_calculation_rule_translations_language_id_foreign');
		});
	}

}
