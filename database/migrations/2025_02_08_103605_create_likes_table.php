<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vacation_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'vacation_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
