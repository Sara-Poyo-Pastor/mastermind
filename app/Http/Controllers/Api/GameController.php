<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with('moves')->get();
        $games->each(function ($game) {
            $game->makeHidden('code');
        });
        return response()->json($games);
    }

    public function show($id)
    {
        $game = Game::with('moves')->findOrFail($id);
        $game->makeHidden('code');
        return response()->json($game);
    }

    public function store(Request $request)
    {
        $name = $request->input('name', 'Unnamed Game');
        $code = $this->generateSecretCode();

        $game = Game::create([
            'name'   => $name,
            'code'   => $code,
            'status' => 'in_progress'
        ]);

        $game->makeHidden('code');

        return response()->json($game, 201);
    }

    private function generateSecretCode()
    {
        $colors = ['rojo', 'azul', 'verde', 'amarillo', 'naranja', 'morado'];
        shuffle($colors);
        return array_slice($colors, 0, 4);
    }
}
