<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDegreeLevelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('degree_levels', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191)->nullable()->unique();
			$table->integer('created_by')->unsigned()->index('degree_levels_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('degree_levels_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('degree_levels_deleted_by_foreign');
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
		Schema::drop('degree_levels');
	}

}
