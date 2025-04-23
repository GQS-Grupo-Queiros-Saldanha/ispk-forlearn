<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('languages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191);
			$table->string('code', 191);
			$table->boolean('default')->default(0);
			$table->boolean('active')->default(1);
			$table->integer('created_by')->unsigned()->index('languages_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('languages_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('languages_deleted_by_foreign');
			$table->softDeletes();
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
		Schema::drop('languages');
	}

}
