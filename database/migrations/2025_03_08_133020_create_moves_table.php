<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->json('guessed_colors'); 
            $table->json('evaluation');     
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moves');
    }
};
