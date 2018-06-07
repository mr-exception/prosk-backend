<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('description');
            $table->integer('user_id')->foreign()->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('finish_time');
            $table->dateTime('started_at');
            $table->dateTime('finished_at');
            $table->smallInteger('status')->default(1);
            $table->smallInteger('poritory')->default(5);
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
        Schema::dropIfExists('tasks');
    }
}
