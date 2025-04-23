<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDaysOfTheWeekTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('days_of_the_week', function(Blueprint $table)
		{
			$table->foreign('created_by', 'days_of_the_week_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('updated_by', 'days_of_the_week_ibfk_2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('deleted_by', 'days_of_the_week_ibfk_3')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('days_of_the_week', function(Blueprint $table)
		{
			$table->dropForeign('days_of_the_week_ibfk_1');
			$table->dropForeign('days_of_the_week_ibfk_2');
			$table->dropForeign('days_of_the_week_ibfk_3');
		});
	}

}
