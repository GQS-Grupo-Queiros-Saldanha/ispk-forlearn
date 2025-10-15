<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDisciplineRegimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('discipline_regimes', function(Blueprint $table)
		{
			$table->foreign('created_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('deleted_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('updated_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('discipline_regimes', function(Blueprint $table)
		{
			$table->dropForeign('discipline_regimes_created_by_foreign');
			$table->dropForeign('discipline_regimes_deleted_by_foreign');
			$table->dropForeign('discipline_regimes_updated_by_foreign');
		});
	}

}
