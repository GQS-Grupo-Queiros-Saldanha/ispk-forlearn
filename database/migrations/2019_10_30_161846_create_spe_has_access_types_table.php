<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSpeHasAccessTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spe_has_access_types', function(Blueprint $table)
		{
			$table->increments('spe_id');
			$table->integer('access_type_id')->unsigned()->index('spe_has_at_access_types_id_foreign');
			$table->integer('max_enrollments')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('spe_has_access_types');
	}

}
