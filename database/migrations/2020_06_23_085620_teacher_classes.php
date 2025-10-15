<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeacherClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_classes', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->index('user_classes_user_id_foreign');
            $table->integer('class_id')->unsigned()->index('user_classes_class_id_foreign');

            $table->integer('lective_year');
            $table->timestamps();

            $table->index('user_id', 'user_classes_user_id');
            $table->index('class_id', 'user_classes_class_id');

            $table->foreign('user_id')->references('id')->on('users')
                                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('class_id')->references('id')->on('classes')
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
        Schema::dropIfExists('teacher_classes');

    }
}
