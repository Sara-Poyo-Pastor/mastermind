<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id', 'code_proposed', 'result'
    ];

    protected $casts = [
        'code_proposed' => 'array',
        'result' => 'array'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
