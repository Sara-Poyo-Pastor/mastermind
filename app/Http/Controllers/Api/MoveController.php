<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Move;

class MoveController extends Controller
{
    public function store(Request $request, $gameId)
    {
        $game = Game::findOrFail($gameId);

        // Devuelve error si ya ha acabado
        if ($game->status !== 'in_progress') {
            return response()->json(['error' => 'The game has already finished'], 400);
        }

        // Validación del movimiento
        $request->validate([
            'code' => 'required|array|size:4',
            'code.*' => 'required|string|in:red,blue,green,yellow,orange,purple'
        ]);

        $codeProposed = $request->input('code');

        // Comprobación de que los colores no se repiten en el movimiento
        if (count($codeProposed) !== count(array_unique($codeProposed))) {
            return response()->json(['error' => 'Duplicate colors are not allowed in the move'], 400);
        }

        // Evaluación del movimiento
        $result = $this->evaluateGuess($game->code, $codeProposed);

        // Crear el registro del movimiento
        $move = Move::create([
            'game_id' => $game->id,
            'code_proposed' => $codeProposed,
            'result' => $result
        ]);

        // Contabilizar el número de movimientos realizados
        $movesCount = $game->moves()->count() + 1;

        // Actualización del estado del juego
        if ($result['exact'] == 4) {
            $game->status = 'victory';
        } elseif ($movesCount >= 10) {
            $game->status = 'defeat';
        }
        $game->save();

        return response()->json([
            'move' => $move,
            'game_status' => $game->status,
            'moves_remaining' => max(0, 10 - $movesCount)
        ]);
    }

    // Comprobación del movimiento con el código secreto 
    private function evaluateGuess(array $secret, array $guess)
    {
        $exact = 0;
        foreach ($secret as $i => $color) {
            if ($guess[$i] == $color) {
                $exact++;
            }
        }
        $common = count(array_intersect($secret, $guess));
        $partial = $common - $exact;
        return ['exact' => $exact, 'partial' => $partial];
    }
};

