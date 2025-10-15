<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PercentageAvaliation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('percentage_avaliation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('avaliacaos_id')->unsigned();
            $table->foreign('avaliacaos_id')->references('id')->on('avaliacaos')->onDelete('cascade');
            $table->integer('nota');
            $table->integer('state');
            $table->unsignedInteger('discipline_id')->nullable();
            $table->foreign('discipline_id')->references('id')->on('disciplines')
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
        Schema::table('percentage_avaliation', function (Blueprint $table) {
            //
        });
    }
}
