<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSummaryTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summary_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('summaries_id');
            $table->unsignedInteger('language_id');
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->integer('version')->default(1);
            $table->tinyInteger('active')->default(true);
            $table->nullableTimestamps();
            $table->softDeletes();

            $table->index('summaries_id', 'summaries_translations_summaries_id');
            $table->index('language_id', 'summaries_translations_language_id');

            $table->foreign('summaries_id', 'summaries_translations_ibfk_1')->references('id')
                ->on('summaries')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('language_id', 'summaries_translations_ibfk_2')->references('id')
                ->on('languages')->onDelete('RESTRICT')->onUpdate('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('summary_translations');
    }
}
