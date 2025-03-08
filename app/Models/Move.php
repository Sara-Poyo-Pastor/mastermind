<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id', 'guessed_colors', 'evaluation'
    ];

    protected $casts = [
        'guessed_colors' => 'array',
        'evaluation' => 'array',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
