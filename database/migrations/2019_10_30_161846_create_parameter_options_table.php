<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateParameterOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('parameter_options', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('parameters_id')->unsigned()->index('parameter_options_parameters_id_foreign');
			$table->string('code', 191)->nullable()->unique();
			$table->boolean('has_related_parameters')->default(0);
			$table->integer('created_by')->unsigned()->index('parameter_options_created_by_foreign');
			$table->integer('updated_by')->unsigned()->nullable()->index('parameter_options_updated_by_foreign');
			$table->integer('deleted_by')->unsigned()->nullable()->index('parameter_options_deleted_by_foreign');
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
		Schema::drop('parameter_options');
	}

}
