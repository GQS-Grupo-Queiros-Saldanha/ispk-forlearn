<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatriculations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matriculations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned()->index('matriculations_user_id_foreign');
            $table->string('code');
            $table->unsignedInteger('course_year');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->nullableTimestamps();
            $table->softDeletes();

            $table->index('user_id', 'matriculations_user_id');

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');

            $table->foreign('created_by', 'matriculations_created_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('updated_by', 'matriculations_updated_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('deleted_by', 'matriculations_deleted_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
        });

        Schema::create('matriculation_disciplines', function (Blueprint $table) {
            $table->bigInteger('matriculation_id')->unsigned()->index('matriculation_disciplines_matriculation_id_foreign');
            $table->integer('discipline_id')->unsigned()->index('matriculation_disciplines_discipline_id_foreign');
            $table->boolean('exam_only')->default(false);

            $table->index('matriculation_id', 'matriculation_disciplines_matriculation_id');
            $table->index('discipline_id', 'matriculation_disciplines_discipline_id');

            $table->foreign('matriculation_id')->references('id')->on('matriculations')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('discipline_id')->references('id')->on('disciplines')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });

        Schema::create('matriculation_classes', function (Blueprint $table) {
            $table->bigInteger('matriculation_id')->unsigned()->index('matriculation_classes_matriculation_id_foreign');
            $table->integer('class_id')->unsigned()->index('matriculation_classes_class_id_foreign');

            $table->index('matriculation_id', 'matriculation_classes_matriculation_id');
            $table->index('class_id', 'matriculation_classes_class_id');

            $table->foreign('matriculation_id')->references('id')->on('matriculations')
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
        Schema::dropIfExists('matriculation_classes');
        Schema::dropIfExists('matriculation_disciplines');
        Schema::dropIfExists('matriculations');
    }
}
