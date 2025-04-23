<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOptionalGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('optional_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191)->nullable()->unique();
			$table->integer('created_by')->unsigned()->index('optional_groups_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('optional_groups_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('optional_groups_deleted_by_foreign');
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
		Schema::drop('optional_groups');
	}

}
