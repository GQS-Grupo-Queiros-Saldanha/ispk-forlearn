<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191)->default('')->unique();
			$table->integer('created_by')->unsigned()->index('event_types_created_by');
			$table->integer('updated_by')->unsigned()->nullable()->index('event_types_updated_by');
			$table->integer('deleted_by')->unsigned()->nullable()->index('event_types_deleted_by');
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
		Schema::drop('event_types');
	}

}
