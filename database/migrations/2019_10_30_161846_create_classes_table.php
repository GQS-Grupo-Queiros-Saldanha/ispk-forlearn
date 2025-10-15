<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClassesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('classes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191)->unique();
			$table->string('display_name', 191);
			$table->integer('created_by')->unsigned()->index('classes_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('classes_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('classes_deleted_by_foreign');
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
		Schema::drop('classes');
	}

}
