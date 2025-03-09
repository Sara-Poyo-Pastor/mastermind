<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Move;
use OpenApi\Annotations as OA;


class MoveController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/games/{gameId}/moves",
     *     summary="Realiza un movimiento en el juego",
     *     tags={"Moves"},
     *     @OA\Parameter(
     *         name="gameId",
     *         in="path",
     *         required=true,
     *         description="ID del juego",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="code", type="array", @OA\Items(type="string"), example={"rojo", "azul", "verde", "amarillo"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Movimiento registrado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="move", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="game_id", type="integer"),
     *                 @OA\Property(property="guessed_colors", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="evaluation", type="object",
     *                     @OA\Property(property="exact", type="integer"),
     *                     @OA\Property(property="partial", type="integer")
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="game_status", type="string"),
     *             @OA\Property(property="moves_remaining", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la jugada (colores repetidos o el juego ya ha terminado)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Juego no encontrado"
     *     )
     * )
     */
    public function store(Request $request, $gameId)
    {
        $game = Game::findOrFail($gameId);

        if ($game->status !== 'jugando...') {
            return response()->json(['error' => 'El juego ya ha finalizado'], 400);
        }

        $data = $request->validate([
            'code' => 'required|array|size:4',
            'code.*' => 'required|string|in:rojo,azul,verde,amarillo,naranja,morado'
        ]);

        $codeProposed = $data['code'];

        if (count($codeProposed) !== count(array_unique($codeProposed))) {
            return response()->json(['error' => 'No se permiten colores duplicados en la jugada'], 400);
        }

        $evaluation = $this->evaluateGuess($game->code, $codeProposed);

        // Convierte array a JSON manualmente, porque MySQL usa longtext (no nativo JSON)
        $move = Move::create([
            'game_id'       => $game->id,
            'guessed_colors'=> json_encode($codeProposed),
            'evaluation'    => json_encode($evaluation)
        ]);

        $movesCount = Move::where('game_id', $game->id)->count();

        if ($evaluation['exact'] == 4) {
            $game->status = 'Has ganado!';
        } elseif ($movesCount >= 10) {
            $game->status = 'Oh no! Has perdido :(';
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

