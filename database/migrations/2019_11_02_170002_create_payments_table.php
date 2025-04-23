<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('article_id');
            $table->unsignedInteger('month')->nullable()->default(null);
            $table->string('transaction_uid')->nullable()->default(null);
            $table->double('total_paid')->default(0);
            $table->double('total_value');
            $table->double('base_value');
            $table->double('extra_fee')->nullable()->default(null);
            $table->text('free_text')->nullable()->default(null);
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamp('fulfilled_at')->nullable()->default(null);
            $table->nullableTimestamps();
            $table->softDeletes();

            $table->index('article_id', 'payments_article_id');
            $table->index('user_id', 'payments_user_id');

            $table->foreign('article_id', 'payments_article_id_foreign')->references('id')
                ->on('articles')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('user_id', 'payments_user_id_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('created_by', 'payments_created_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('deleted_by', 'payments_deleted_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('updated_by', 'payments_updated_by_foreign')->references('id')
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
        Schema::dropIfExists('payments');
    }
}
