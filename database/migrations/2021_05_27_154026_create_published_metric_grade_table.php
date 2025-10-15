<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublishedMetricGradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('published_metric_grade', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('study_plan_edition_id');
            $table->integer('discipline_id');
            $table->integer('avaliation_id');
            $table->integer('class_id');
            $table->integer('metric_id');
            $table->integer('lecive_year');
            $table->boolean('published')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('published_metric_grade');
    }
}
