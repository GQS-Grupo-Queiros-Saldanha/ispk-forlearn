<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoAvaliacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_avaliacaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome', 150);


            $table->integer('created_by')->unsigned()->index('tipos_avaliacoes_created_by_by_foreign');
            $table->integer('updated_by')->unsigned()->nullable()->index('tipos_avaliacoes_updated_by_by_foreign');
            $table->integer('deleted_by')->unsigned()->nullable()->index('tipos_avaliacoes_deleted_by_by_foreign');

            //Foreign Keys
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
        Schema::dropIfExists('tipo_avaliacaos');
    }
}
