<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCourseIdToUsersStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_states', function (Blueprint $table) {
            $table->unsignedInteger('courses_id')->nullable();
            $table->foreign('courses_id')->references('id')->on('courses')
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
        Schema::table('users_states', function (Blueprint $table) {
            //
        });
    }
}
