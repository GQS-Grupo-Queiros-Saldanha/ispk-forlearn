<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvaliacaoAlunoHistorico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avaliacao_aluno_historicos', function (Blueprint $table) {
            $table->bigIncrements('id');$table->bigInteger('plano_estudo_avaliacaos_id')->unsigned();

            $table->integer('user_id')->unsigned();
            $table->decimal('nota_final', 8, 2)->nullable();
            $table->bigInteger('avaliacaos_id')->unsigned();

            
            $table->integer('created_by')->unsigned()->index('avaliacao_aluno_historicos_created_by_by_foreign');
            $table->integer('updated_by')->unsigned()->nullable()->index('avaliacao_aluno_historicos_updated_by_by_foreign');
            $table->integer('deleted_by')->unsigned()->nullable()->index('avaliacao_aluno_historicos_deleted_by_by_foreign');
            
            //Foreign Keys
            
            $table->foreign('plano_estudo_avaliacaos_id')->references('id')->on('plano_estudo_avaliacaos')->onDelete('cascade');

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('avaliacaos_id')->references('id')->on('avaliacaos')->onDelete('cascade');
            
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('deleted_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');

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
        Schema::dropIfExists('avaliacao_aluno_historicos');
    }
}
