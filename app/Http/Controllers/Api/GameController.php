<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;

class GameController extends Controller
{
    // Lista todas las partidas (oculta el código secreto)
    public function index()
    {
        $games = Game::with('moves')->get();
        $games->each(function ($game) {
            $game->makeHidden('code');
        });
        return response()->json($games);
    }

    // Muestra los detalles de una partida (incluye los movimientos, sin el código secreto)
    public function show($id)
    {
        $game = Game::with('moves')->findOrFail($id);
        $game->makeHidden('code');
        return response()->json($game);
    }

    // Crea una nueva partida: recibe opcionalmente un nombre, genera un código secreto y devuelve la partida sin revelar el código
    public function store(Request $request)
    {
        $name = $request->input('name', 'Unnamed Game');
        $code = $this->generateSecretCode();

        $game = Game::create([
            'name'   => $name,
            'code'   => $code,
            'status' => 'in_progress'
        ]);

        // Ocultar el código secreto antes de devolver la respuesta
        $game->makeHidden('code');

        return response()->json($game, 201);
    }

    // Método auxiliar para generar un código secreto de 4 colores sin repetición
    private function generateSecretCode()
    {
        $colors = ['rojo', 'azul', 'verde', 'amarillo', 'naranja', 'morado'];
        shuffle($colors);
        return array_slice($colors, 0, 4);
    }
}
