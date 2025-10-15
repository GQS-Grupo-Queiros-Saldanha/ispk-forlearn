<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenuItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menu_items', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191)->unique();
			$table->integer('created_by')->unsigned()->index('menu_items_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('menu_items_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('menu_items_deleted_by_foreign');
			$table->integer('parent_id')->unsigned()->nullable()->index('menu_items_parent_id_foreign');
			$table->integer('menus_id')->unsigned()->nullable()->index('menu_items_menus_id_foreign');
			$table->integer('position');
			$table->string('icon', 191)->nullable();
			$table->text('external_link', 65535)->nullable();
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
		Schema::drop('menu_items');
	}

}
