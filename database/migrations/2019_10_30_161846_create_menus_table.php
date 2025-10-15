<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191)->unique();
			$table->integer('order')->nullable()->default(0);
			$table->integer('created_by')->unsigned()->index('menus_created_by');
			$table->integer('updated_by')->unsigned()->nullable()->index('menus_updated_by');
			$table->integer('deleted_by')->unsigned()->nullable()->index('menus_deleted_by');
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
		Schema::drop('menus');
	}

}
