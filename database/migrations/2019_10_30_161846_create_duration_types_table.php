<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDurationTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('duration_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191);
			$table->integer('created_by')->unsigned()->index('duration_types_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('duration_types_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('duration_types_deleted_by_foreign');
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
		Schema::drop('duration_types');
	}

}
