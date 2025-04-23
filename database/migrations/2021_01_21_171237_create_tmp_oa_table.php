<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmpOaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_oa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('grade', 8, 2);
            $table->bigInteger('metricas_id')->nullable();
            $table->integer('oa_number');
            $table->bigInteger('avaliacaos_id')->unsigned();
            $table->foreign('avaliacaos_id')->references('id')->on('avaliacaos')->onDelete('cascade');
            $table->unsignedInteger('discipline_id')->nullable();
            $table->foreign('discipline_id')->references('id')->on('disciplines')
                  ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->unsignedInteger('courses_id')->nullable();
            $table->foreign('courses_id')->references('id')->on('courses')
                  ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->unsignedInteger('class_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmp_oa');
    }
}
