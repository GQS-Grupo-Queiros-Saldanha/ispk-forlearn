<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudyPlanEditionDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_plan_edition_disciplines', function (Blueprint $table) {
            $table->integer('study_plan_edition_id')->unsigned()->index('study_plan_edition_disciplines_study_plan_edition_id_foreign');
            $table->integer('discipline_id')->unsigned()->index('study_plan_edition_disciplines_discipline_id_foreign');

            $table->index('study_plan_edition_id', 'study_plan_edition_disciplines_study_plan_edition_id');
            $table->index('discipline_id', 'study_plan_edition_disciplines_discipline_id');

            $table->foreign('study_plan_edition_id')->references('id')->on('study_plan_editions')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('discipline_id')->references('id')->on('disciplines')
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
        Schema::dropIfExists('study_plan_edition_disciplines');
    }
}
