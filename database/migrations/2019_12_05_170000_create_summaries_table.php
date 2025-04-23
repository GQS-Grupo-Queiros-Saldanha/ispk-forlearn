<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('study_plan_id');
            $table->unsignedInteger('discipline_id');
            $table->unsignedInteger('discipline_regime_id');
            $table->unsignedInteger('order');
            $table->text('content');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->nullableTimestamps();
            $table->softDeletes();

            $table->index('study_plan_id', 'summaries_study_plan_id');
            $table->index('discipline_id', 'summaries_discipline_id');
            $table->index('discipline_regime_id', 'summaries_discipline_regime_id');

            $table->foreign('created_by', 'summaries_created_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('deleted_by', 'summaries_deleted_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('updated_by', 'summaries_updated_by_foreign')->references('id')
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
        Schema::dropIfExists('summaries');
    }
}
