<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'status'
    ];

    protected $casts = [
        'code' => 'array'
    ];

    public function moves()
    {
        return $this->hasMany(Move::class);
    }
}
