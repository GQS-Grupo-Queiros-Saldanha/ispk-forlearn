<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('article_id');
            $table->unsignedInteger('year')->nullable()->default(null);
            $table->unsignedInteger('month')->nullable()->default(null);
            $table->double('base_value');
            $table->double('extra_fees_value')->default(0);
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->nullableTimestamps();
            $table->softDeletes();

            $table->index('user_id', 'article_requests_user_id');
            $table->index('article_id', 'article_requests_article_id');

            $table->foreign('user_id', 'article_requests_user_id_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('article_id', 'article_requests_article_id_foreign')->references('id')
                ->on('articles')->onDelete('RESTRICT')->onUpdate('RESTRICT');

            $table->foreign('created_by', 'article_requests_created_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('deleted_by', 'article_requests_deleted_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('updated_by', 'article_requests_updated_by_foreign')->references('id')
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
        Schema::dropIfExists('article_requests');
    }
}
