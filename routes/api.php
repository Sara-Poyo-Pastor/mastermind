<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\MoveController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar rutas de API para tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider dentro de un grupo que
| tiene asignado el middleware "api". ¡Disfruta construyendo tu API!
|
*/

// Lista de juegos
Route::get('games', [GameController::class, 'index']);

// Detalle de un juego (incluye movimientos)
Route::get('games/{id}', [GameController::class, 'show']);

// Creación de un nuevo juego
Route::post('games', [GameController::class, 'store']);

// Envío de un movimiento para un juego
Route::post('games/{id}/moves', [MoveController::class, 'store']);
