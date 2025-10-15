<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('bank_id');
            $table->timestamp('fulfilled_at')->nullable()->default(null);
            $table->string('reference')->nullable()->default(null);
            // $table->string('uid')->nullable()->default(null);

            $table->index('transaction_id', 'transaction_info_transaction_id');
            $table->index('bank_id', 'transaction_info_bank_id');

            $table->foreign('transaction_id', 'transaction_info_transaction_id_foreign')->references('id')
                ->on('transactions')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('bank_id', 'transaction_info_bank_id_foreign')->references('id')
                ->on('banks')->onDelete('RESTRICT')->onUpdate('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_info');
    }
}
