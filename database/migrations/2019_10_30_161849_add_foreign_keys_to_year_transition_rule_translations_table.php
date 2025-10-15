<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToYearTransitionRuleTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('year_transition_rule_translations', function(Blueprint $table)
		{
			$table->foreign('language_id')->references('id')->on('languages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('ytr_id')->references('id')->on('year_transition_rules')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('year_transition_rule_translations', function(Blueprint $table)
		{
			$table->dropForeign('year_transition_rule_translations_language_id_foreign');
			$table->dropForeign('year_transition_rule_translations_ytr_id_foreign');
		});
	}

}
