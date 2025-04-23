<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateParametersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('parameters', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 191)->nullable()->unique();
			$table->integer('created_by')->unsigned()->index('parameters_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('parameters_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('parameters_deleted_by_foreign');
			$table->timestamps();
			$table->softDeletes();
			$table->string('type', 191)->nullable();
			$table->boolean('has_options')->nullable();
			$table->boolean('required')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('parameters');
	}

}
