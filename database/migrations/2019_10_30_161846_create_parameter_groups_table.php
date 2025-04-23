<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateParameterGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('parameter_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191)->nullable()->unique();
			$table->integer('order')->default(0);
			$table->integer('created_by')->unsigned()->index('parameter_groups_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('parameter_groups_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('parameter_groups_deleted_by_foreign');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('parameter_groups');
	}

}
