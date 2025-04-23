<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToPercentageAvaliationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('percentage_avaliation', function (Blueprint $table) {
                $table->integer('percentage_mac');
                $table->integer('percentage_neen');
                $table->integer('class_id');
                $table->integer('plano_estudo_avaliacaos_id');
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
