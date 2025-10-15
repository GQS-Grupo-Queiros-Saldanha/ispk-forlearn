<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionArticleRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_article_requests', function (Blueprint $table) {
            $table->bigInteger('transaction_id')->unsigned()->index('transaction_article_requests_transaction_id_foreign');
            $table->bigInteger('article_request_id')->unsigned()->index('transaction_article_requests_article_request_id_foreign');
            $table->double('value')->default(0);

            $table->index('transaction_id', 'transaction_article_requests_transaction_id');
            $table->index('article_request_id', 'transaction_article_requests_article_request_id');

            $table->foreign('transaction_id')->references('id')->on('transactions')
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
        Schema::dropIfExists('transaction_article_requests');
    }
}
