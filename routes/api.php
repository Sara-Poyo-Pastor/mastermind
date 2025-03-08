<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\MoveController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas de API para la aplicación. Estas rutas se
| cargan a través del RouteServiceProvider con el middleware "api".
|
*/

// Lista de partidas en juego
Route::get('games', [GameController::class, 'index']);

// Consulta de los datos de una partida (incluye jugadas realizadas)
Route::get('games/{id}', [GameController::class, 'show']);

// Creación de una nueva partida (recibe opcionalmente un nombre, genera un código secreto y devuelve la partida sin el código)
Route::post('games', [GameController::class, 'store']);

// Envío de una jugada para una partida (recibe la suposición, evalúa el movimiento y responde con el resultado y el estado de la partida)
Route::post('games/{gameId}/move', [MoveController::class, 'store']);

