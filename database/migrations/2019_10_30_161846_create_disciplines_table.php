<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDisciplinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('disciplines', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('discipline_profiles_id')->unsigned()->index('disciplines_discipline_profiles_id_foreign');
			$table->string('code', 191)->nullable()->unique();
			$table->integer('created_by')->unsigned()->index('disciplines_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('disciplines_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('disciplines_deleted_by_foreign');
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
		Schema::drop('disciplines');
	}

}
