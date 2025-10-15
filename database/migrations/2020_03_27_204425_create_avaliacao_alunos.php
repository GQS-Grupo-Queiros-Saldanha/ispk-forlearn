<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvaliacaoAlunos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avaliacao_alunos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('plano_estudo_avaliacaos_id')->unsigned();
            $table->bigInteger('metricas_id')->unsigned();
            //$table->bigInteger('avaliacao_estados_id')->unsigned();

            $table->integer('users_id')->unsigned();
            $table->decimal('nota', 8, 2)->nullable();

            $table->integer('created_by')->unsigned()->index('avaliacao_alunos_created_by_by_foreign');
            $table->integer('updated_by')->unsigned()->nullable()->index('avaliacao_alunos_updated_by_by_foreign');
            $table->integer('deleted_by')->unsigned()->nullable()->index('avaliacao_alunos_deleted_by_by_foreign');

            //Foreign Keys
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('deleted_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');

            $table->foreign('plano_estudo_avaliacaos_id')->references('id')->on('plano_estudo_avaliacaos')->onDelete('cascade');
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('metricas_id')->references('id')->on('metricas')->onDelete('cascade');
            //$table->foreign('avaliacao_estados_id')->references('id')->on('avaliacao_estados')->onDelete('cascade');

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
        Schema::dropIfExists('avaliacao_alunos');
    }
}
