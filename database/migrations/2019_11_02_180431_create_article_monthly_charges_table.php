<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleMonthlyChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_monthly_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('article_id');
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('course_year')->nullable();
            $table->unsignedInteger('start_month');
            $table->unsignedInteger('end_month');
            $table->unsignedInteger('charge_day');

            $table->foreign('article_id', 'article_has_monthly_recurring_charge_article_id_foreign')
                ->references('id')->on('articles')
                ->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('course_id', 'article_has_monthly_recurring_charge_course_id_foreign')
                ->references('id')->on('courses')
                ->onDelete('RESTRICT')->onUpdate('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_monthly_charges');
    }
}
