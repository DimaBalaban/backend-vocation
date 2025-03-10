<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVacationLikesTable extends Migration
{
    /**

     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacation_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('vacation_id');
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vacation_id')->references('id')->on('vacations')->onDelete('cascade');
        });
    }

    /**

     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacation_likes');
    }
}
