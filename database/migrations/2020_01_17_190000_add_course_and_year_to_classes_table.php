<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCourseAndYearToClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->unsignedInteger('courses_id')->nullable();
            $table->unsignedInteger('year')->default(0);

            $table->index('courses_id', 'disciplines_course_id');

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
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign('courses_id');

            $table->dropIndex('courses_id');

            $table->dropColumn('courses_id');
            $table->dropColumn('year');
        });
    }
}
