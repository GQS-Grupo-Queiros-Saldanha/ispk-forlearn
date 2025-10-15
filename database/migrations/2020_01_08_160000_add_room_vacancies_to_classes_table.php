<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddRoomVacanciesToClassesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('classes', function (Blueprint $table) {
            $table->unsignedInteger('room_id')->nullable();
            $table->unsignedInteger('vacancies')->default(0);

            $table->index('room_id', 'classes_room_id');

            $table->foreign('room_id')->references('id')->on('rooms')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign('room_id');

            $table->dropIndex('room_id');

            $table->dropColumn('room_id');
            $table->dropColumn('vacancies');
        });
	}

}
