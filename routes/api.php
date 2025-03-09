<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\MoveController;



Route::get('games', [GameController::class, 'index']);

Route::get('games/{id}', [GameController::class, 'show']);

Route::post('games', [GameController::class, 'store']);

Route::post('games/{gameId}/move', [MoveController::class, 'store']);

