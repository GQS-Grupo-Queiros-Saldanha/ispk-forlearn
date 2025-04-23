<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMenuItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('menu_items', function(Blueprint $table)
		{
			$table->foreign('menus_id', 'menu_items_ibfk_1')->references('id')->on('menus')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('menu_items', function(Blueprint $table)
		{
			$table->dropForeign('menu_items_ibfk_1');
		});
	}

}
