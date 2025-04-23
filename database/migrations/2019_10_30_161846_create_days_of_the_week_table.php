<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDaysOfTheWeekTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('days_of_the_week', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191)->unique();
			$table->boolean('is_start_of_week')->default(0);
			$table->integer('created_by')->unsigned()->index('days_of_the_week_created_by');
			$table->integer('updated_by')->unsigned()->nullable()->index('days_of_the_week_ibfk_2');
			$table->integer('deleted_by')->unsigned()->nullable()->index('days_of_the_week_ibfk_3');
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
		Schema::drop('days_of_the_week');
	}

}
