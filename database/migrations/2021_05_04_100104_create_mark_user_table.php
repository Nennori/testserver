<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarkUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mark_user', function (Blueprint $table) {
            $table->primary(['mark_id', 'task_id']);
            $table->uuid('mark_id');
            $table->foreign('mark_id')->references('id')->on('marks')->onDelete('cascade');
            $table->uuid('task_id');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mark_user');
    }
}
