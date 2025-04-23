<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetricasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metricas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('nome', 150);
            $table->integer('percentagem');

            $table->integer('created_by')->unsigned()->index('metricas_created_by_by_foreign');
            $table->integer('updated_by')->unsigned()->nullable()->index('metricas_updated_by_by_foreign');
            $table->integer('deleted_by')->unsigned()->nullable()->index('metricas_deleted_by_by_foreign');


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
        Schema::dropIfExists('metricas');
    }
}
