<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewOldGrade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_old_grades', function (Blueprint $table) {
     $table->bigIncrements('id');
     $table->unsignedInteger('user_id');
     $table->unsignedInteger('discipline_id');
     $table->integer('lective_year');
     $table->decimal('grade', 8, 2);


     $table->foreign('user_id')->references('id')->on('users');
     $table->foreign('discipline_id')->references('id')->on('disciplines');
     $table->timestamps();
 });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_old_grades');

    }
}
