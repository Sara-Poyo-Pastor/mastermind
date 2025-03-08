<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->json('code'); 
            $table->string('status')->default('in_progress'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('games');
    }
}

