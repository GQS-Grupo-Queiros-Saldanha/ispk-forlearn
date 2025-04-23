<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisciplinesArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disciplines_articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('discipline_id')->unsigned();
            $table->integer('article_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            /*$table->foreign('discipline_id')->references('id')->on('disciplines')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');

            $table->foreign('article_id')->references('id')->on('articles')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disciplines_articles');
    }
}
