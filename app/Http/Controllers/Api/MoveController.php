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
        // Buscar la partida; si no existe se lanza 404
        $game = Game::findOrFail($gameId);

        // Verificar que la partida esté en curso
        if ($game->status !== 'in_progress') {
            return response()->json(['error' => 'El juego ya ha finalizado'], 400);
        }

        // Validar la jugada: se espera un arreglo de 4 colores permitidos
        $data = $request->validate([
            'code' => 'required|array|size:4',
            // Aquí incluimos tanto los nombres en inglés como en castellano, si es necesario
            'code.*' => 'required|string|in:red,blue,green,yellow,orange,purple,rojo,azul,verde,amarillo,naranja,morado'
        ]);

        $codeProposed = $data['code'];

        // Verificar que no se repitan colores en la jugada
        if (count($codeProposed) !== count(array_unique($codeProposed))) {
            return response()->json(['error' => 'No se permiten colores duplicados en la jugada'], 400);
        }

        // Evaluar la jugada comparándola con el código secreto almacenado en la partida
        $evaluation = $this->evaluateGuess($game->code, $codeProposed);

        // Crear el movimiento: 
        // Convertimos los arrays a JSON manualmente, ya que MySQL usa longtext (no nativo JSON).
        $move = Move::create([
            'game_id'       => $game->id,
            'guessed_colors'=> json_encode($codeProposed),
            'evaluation'    => json_encode($evaluation)
        ]);

        // Contabilizar el número de movimientos realizados usando una consulta directa
        $movesCount = Move::where('game_id', $game->id)->count();

        // Actualizar el estado del juego:
        // Si la jugada es perfecta (4 exactos), se marca como "victory"
        // Si se han realizado 10 o más jugadas sin ganar, se marca como "defeat"
        if ($evaluation['exact'] == 4) {
            $game->status = 'victory';
        } elseif ($movesCount >= 10) {
            $game->status = 'defeat';
        }
        $game->save();

        // Devolver la respuesta decodificando los datos almacenados en JSON
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

    // Evalúa la jugada comparando el código secreto con la suposición del jugador.
    // Retorna un array con:
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
