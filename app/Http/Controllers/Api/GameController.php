<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;

class GameController extends Controller
{
    // Mostrar todos los juegos 
    public function index()
    {
        $games = Game::with('moves')->get();
        $games->each(function($game) {
            unset($game->code);
        });
        return response()->json($games);
    }

    // Mostrar los detalles de un juego
    public function show($id)
    {
        $game = Game::with('moves')->findOrFail($id);
        $game->makeHidden('code');
        return response()->json($game);
    }

    // Crear un nuevo juego
    public function store(Request $request)
    {
        $name = $request->input('name', 'Unnamed Game');
        $code = $this->generateSecretCode();
        $game = Game::create([
            'name' => $name,
            'code' => $code,
            'status' => 'in_progress'
        ]);
        return response()->json($game, 201);
    }

    // Genera un código secreto aleatorio
    private function generateSecretCode()
    {
        $colors = ['red', 'blue', 'green', 'yellow', 'orange', 'purple'];
        shuffle($colors);
        return array_slice($colors, 0, 4);
    }
}
