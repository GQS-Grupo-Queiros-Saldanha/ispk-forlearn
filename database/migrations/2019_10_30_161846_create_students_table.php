<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('students', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('users_id')->unsigned()->index('students_users_id')->comment('ID do utilizador associado');
			$table->string('number', 191)->nullable();
			$table->timestamps();
			$table->integer('created_by')->unsigned()->index('students_created_by')->comment('ID do utilizador que criou este item');
			$table->integer('updated_by')->unsigned()->nullable()->index('students_updated_by')->comment('ID do último utilizador que editou este item');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('students');
	}

}
