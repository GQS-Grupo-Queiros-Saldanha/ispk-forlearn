<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('grades');

        Schema::create('grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('discipline_id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('value');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->nullableTimestamps();
            $table->softDeletes();

            $table->index('course_id', 'grades_course_id');
            $table->index('discipline_id', 'grades_discipline_id');
            $table->index('student_id', 'grades_student_id');

            $table->foreign('course_id')->references('id')->on('courses')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('discipline_id')->references('id')->on('disciplines')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('student_id')->references('id')->on('users')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');

            $table->foreign('created_by', 'grades_created_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('deleted_by', 'grades_deleted_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('updated_by', 'grades_updated_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grades');
    }
}
