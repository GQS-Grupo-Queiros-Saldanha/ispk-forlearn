<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRoomsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rooms', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('building_id')->unsigned()->index('rooms_building_id');
			$table->string('code', 191)->unique();
			$table->integer('created_by')->unsigned()->index('rooms_created_by');
			$table->integer('updated_by')->unsigned()->nullable()->index('rooms_updated_by');
			$table->integer('deleted_by')->unsigned()->nullable()->index('rooms_deleted_by');
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
		Schema::drop('rooms');
	}

}
