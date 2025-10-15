<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAvaliationTypeOnOldGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('old_grades', function (Blueprint $table) {
             $table->bigInteger('tipo_avaliacaos_id')->unsigned();

            //Foreign Keys
            $table->foreign('tipo_avaliacaos_id')->references('id')->on('tipo_avaliacaos')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('old_grades', function (Blueprint $table) {
            //
        });
    }
}
