<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudyPlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('study_plans', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('courses_id')->unsigned();
			$table->string('code', 191);
			$table->integer('created_by')->unsigned()->index('study_plans_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('study_plans_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('study_plans_deleted_by_foreign');
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
		Schema::drop('study_plans');
	}

}
