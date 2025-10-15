<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserParametersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_parameters', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('parameter_group_id')->nullable();
			$table->integer('users_id')->unsigned()->index('user_parameters_users_id_foreign');
			$table->integer('parameters_id')->unsigned()->index('user_parameters_parameters_id_foreign');
			$table->string('value', 191)->nullable();
			$table->string('description', 191)->nullable();
			$table->integer('created_by')->unsigned()->index('user_parameters_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('user_parameters_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('user_parameters_deleted_by_foreign');
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
		Schema::drop('user_parameters');
	}

}
