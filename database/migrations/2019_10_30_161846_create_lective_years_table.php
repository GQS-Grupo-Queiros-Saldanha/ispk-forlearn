<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLectiveYearsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lective_years', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191)->nullable()->unique();
			$table->date('start_date');
			$table->date('end_date');
			$table->integer('created_by')->unsigned()->index('lective_years_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('lective_years_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('lective_years_deleted_by_foreign');
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
		Schema::drop('lective_years');
	}

}
