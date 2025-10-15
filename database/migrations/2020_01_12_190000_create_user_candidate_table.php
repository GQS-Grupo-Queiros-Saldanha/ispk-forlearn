<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCandidateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_candidate', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->index('user_candidate_user_id_foreign');
            $table->string('code');
            $table->timestamp('created_at');

            $table->index('user_id', 'user_candidate_user_id');

            $table->foreign('user_id')->references('id')->on('users')
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
        Schema::dropIfExists('user_candidate');
    }
}
