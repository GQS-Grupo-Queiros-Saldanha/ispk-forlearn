<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInformationsToTransactionInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_info', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_1_id')->nullable();
            $table->unsignedBigInteger('bank_2_id')->nullable();
            $table->string('reference_1')->nullable()->default(null);
            $table->string('reference_2')->nullable()->default(null);
            $table->timestamp('fulfilled_at_1')->nullable()->default(null);
            $table->timestamp('fulfilled_at_2')->nullable()->default(null);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_info', function (Blueprint $table) {
            //
        });
    }
}
