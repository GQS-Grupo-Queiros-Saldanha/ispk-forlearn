<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PlanoEstudoAvaliacaos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plano_estudo_avaliacaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('avaliacaos_id')->unsigned();

            $table->integer('study_plan_editions_id')->unsigned();
            $table->integer('disciplines_id')->unsigned();
            
            $table->integer('created_by')->unsigned()->index('plano_estudo_avaliacaos_created_by_by_foreign');
            $table->integer('updated_by')->unsigned()->nullable()->index('plano_estudo_avaliacaos_updated_by_by_foreign');
            $table->integer('deleted_by')->unsigned()->nullable()->index('plano_estudo_avaliacaos_deleted_by_by_foreign');
            
            //Foreign Keys
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('deleted_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            
            $table->foreign('study_plan_editions_id')->references('id')->on('study_plan_editions')->onDelete('cascade');
            $table->foreign('disciplines_id')->references('id')->on('disciplines')->onDelete('cascade');
            $table->foreign('avaliacaos_id')->references('id')->on('avaliacaos')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plano_estudo_avaliacaos');
    }
}
