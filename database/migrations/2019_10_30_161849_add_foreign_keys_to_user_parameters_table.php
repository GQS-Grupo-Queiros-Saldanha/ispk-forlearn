<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUserParametersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_parameters', function(Blueprint $table)
		{
			$table->foreign('created_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('deleted_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('parameters_id')->references('id')->on('parameters')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('updated_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('users_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_parameters', function(Blueprint $table)
		{
			$table->dropForeign('user_parameters_created_by_foreign');
			$table->dropForeign('user_parameters_deleted_by_foreign');
			$table->dropForeign('user_parameters_parameters_id_foreign');
			$table->dropForeign('user_parameters_updated_by_foreign');
			$table->dropForeign('user_parameters_users_id_foreign');
		});
	}

}
