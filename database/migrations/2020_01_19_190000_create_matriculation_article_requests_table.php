<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatriculationArticleRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matriculation_article_requests', function (Blueprint $table) {
            $table->bigInteger('matriculation_id')->unsigned()->index('matriculation_article_requests_matriculation_id_foreign');
            $table->bigInteger('article_request_id')->unsigned()->index('matriculation_article_requests_article_request_id_foreign');
            $table->boolean('updatable')->default(false);

            $table->index('matriculation_id', 'matriculation_article_requests_matriculation_id');
            $table->index('article_request_id', 'matriculation_article_requests_article_request_id');

            $table->foreign('matriculation_id')->references('id')->on('matriculations')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('article_request_id')->references('id')->on('article_requests')
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
        Schema::dropIfExists('matriculation_article_requests');
    }
}
