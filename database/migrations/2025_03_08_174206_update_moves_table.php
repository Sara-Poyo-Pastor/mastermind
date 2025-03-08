<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('moves', function (Blueprint $table) {
            $table->json('guessed_colors')->change();
            $table->json('evaluation')->change();
        });
    }

    public function down(): void
    {
        Schema::table('moves', function (Blueprint $table) {
            $table->text('guessed_colors')->change();
            $table->text('evaluation')->change();
        });
    }
};
