<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_disciplines', function (Blueprint $table) {
            $table->integer('users_id')->unsigned()->index('user_disciplines_users_id_foreign');
            $table->integer('disciplines_id')->unsigned()->index('user_disciplines_disciplines_id_foreign');

            $table->index('users_id', 'user_disciplines_users_id');
            $table->index('disciplines_id', 'user_disciplines_disciplines_id');

            $table->foreign('users_id')->references('id')->on('users')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('disciplines_id')->references('id')->on('disciplines')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_disciplines');
    }
}
