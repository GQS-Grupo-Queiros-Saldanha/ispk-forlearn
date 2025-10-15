<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoMetricasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_metricas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome');

            $table->integer('created_by')->unsigned()->index('tipo_metricas_created_by_by_foreign');
            $table->integer('updated_by')->unsigned()->nullable()->index('tipo_metricas_historicos_updated_by_by_foreign');
            $table->integer('deleted_by')->unsigned()->nullable()->index('tipo_metricas_historicos_deleted_by_by_foreign');

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
        Schema::dropIfExists('tipo_metricas');
    }
}
