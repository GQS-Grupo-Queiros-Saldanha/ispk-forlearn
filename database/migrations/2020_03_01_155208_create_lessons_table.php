<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('teacher_id');
            $table->unsignedInteger('discipline_id');
            $table->unsignedInteger('class_id');
            $table->unsignedInteger('regime_id');
            $table->unsignedBigInteger('summary_id');
            $table->timestamp('occured_at');
            $table->timestamps();

            $table->index('teacher_id', 'lessons_teacher_id');
            $table->index('discipline_id', 'lessons_discipline_id');
            $table->index('class_id', 'lessons_class_id');
            $table->index('regime_id', 'lessons_regime_id');
            $table->index('summary_id', 'lessons_summary_id');

            $table->foreign('teacher_id')->references('id')->on('users')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('discipline_id')->references('id')->on('disciplines')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('class_id')->references('id')->on('classes')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('regime_id')->references('id')->on('discipline_regimes')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('summary_id')->references('id')->on('summaries')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });

        Schema::create('lesson_attendance', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedInteger('student_id');

            $table->index('lesson_id', 'lesson_attendance_lesson_id');
            $table->index('student_id', 'lesson_attendance_student_id');

            $table->foreign('lesson_id')->references('id')->on('lessons')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('student_id')->references('id')->on('users')
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
        Schema::dropIfExists('lesson_attendance');

        Schema::dropIfExists('lessons');
    }
}
