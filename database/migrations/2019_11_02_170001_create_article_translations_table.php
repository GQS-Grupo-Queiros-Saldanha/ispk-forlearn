<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('article_id');
            $table->unsignedInteger('language_id');
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->integer('version')->default(1);
            $table->tinyInteger('active')->default(true);
            $table->nullableTimestamps();
            $table->softDeletes();

            $table->index('article_id', 'article_translations_article_id');
            $table->index('language_id', 'article_translations_language_id');

            $table->foreign('article_id', 'article_translations_ibfk_1')->references('id')->on('articles')->onDelete('RESTRICT
')->onUpdate('RESTRICT');
            $table->foreign('language_id', 'article_translations_ibfk_2')->references('id')->on('languages')->onDelete('RESTRICT
')->onUpdate('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_translations');
    }
}
