<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovesTable extends Migration
{
    public function up()
    {
        Schema::create('moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->json('code_proposed'); 
            $table->json('result');        
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('moves');
    }
}

