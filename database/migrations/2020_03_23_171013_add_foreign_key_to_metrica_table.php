<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToMetricaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metricas', function (Blueprint $table) {
             $table->bigInteger('avaliacaos_id')->unsigned();
            $table->bigInteger('tipo_metricas_id')->unsigned();

            //Foreign Key
            $table->foreign('avaliacaos_id')->references('id')->on('avaliacaos')->onDelete('cascade');
            $table->foreign('tipo_metricas_id')->references('id')->on('tipo_metricas')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metricas', function (Blueprint $table) {
            //
        });
    }
}
