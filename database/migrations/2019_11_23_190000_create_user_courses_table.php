<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_courses', function (Blueprint $table) {
            $table->integer('users_id')->unsigned()->index('user_courses_users_id_foreign');
            $table->integer('courses_id')->unsigned()->index('user_courses_parameters_id_foreign');

            $table->index('users_id', 'user_courses_users_id');
            $table->index('courses_id', 'user_courses_courses_id');

            $table->foreign('users_id')->references('id')->on('users')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
        Schema::dropIfExists('user_courses');
    }
}
