<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->string('display_name');
            $table->string('account_number')->nullable();
            $table->string('iban')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->nullableTimestamps();
            $table->softDeletes();

            $table->foreign('created_by', 'grades_created_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('deleted_by', 'grades_deleted_by_foreign')->references('id')
                ->on('users')->onDelete('RESTRICT')->onUpdate('RESTRICT');
            $table->foreign('updated_by', 'grades_updated_by_foreign')->references('id')
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
        Schema::dropIfExists('banks');
    }
}
