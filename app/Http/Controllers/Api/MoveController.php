<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Move;

class MoveController extends Controller
{
    public function store(Request $request, $gameId)
    {
        $game = Game::findOrFail($gameId);

        if ($game->status !== 'in_progress') {
            return response()->json(['error' => 'El juego ya ha finalizado'], 400);
        }

        $data = $request->validate([
            'code' => 'required|array|size:4',
            'code.*' => 'required|string|in:red,blue,green,yellow,orange,purple,rojo,azul,verde,amarillo,naranja,morado'
        ]);

        $codeProposed = $data['code'];

        if (count($codeProposed) !== count(array_unique($codeProposed))) {
            return response()->json(['error' => 'No se permiten colores duplicados en la jugada'], 400);
        }

        $evaluation = $this->evaluateGuess($game->code, $codeProposed);

        // Convierte los arrays a JSON manualmente, ya que MySQL usa longtext (no nativo JSON)
        $move = Move::create([
            'game_id'       => $game->id,
            'guessed_colors'=> json_encode($codeProposed),
            'evaluation'    => json_encode($evaluation)
        ]);

        $movesCount = Move::where('game_id', $game->id)->count();

        if ($evaluation['exact'] == 4) {
            $game->status = 'victory';
        } elseif ($movesCount >= 10) {
            $game->status = 'defeat';
        }
        $game->save();

        return response()->json([
            'move' => [
                'id'             => $move->id,
                'game_id'        => $move->game_id,
                'guessed_colors' => json_decode($move->guessed_colors, true),
                'evaluation'     => json_decode($move->evaluation, true),
                'created_at'     => $move->created_at,
            ],
            'game_status'     => $game->status,
            'moves_remaining' => max(0, 10 - $movesCount)
        ], 201);
    }


    // 'exact'   => número de colores en la posición correcta,
    // 'partial' => número de colores correctos pero en posición incorrecta.
    private function evaluateGuess(array $secret, array $guess)
    {
        $exact = 0;
        foreach ($secret as $i => $color) {
            if ($guess[$i] === $color) {
                $exact++;
            }
        }
        $common = count(array_intersect($secret, $guess));
        $partial = $common - $exact;
        return ['exact' => $exact, 'partial' => $partial];
    }
}
