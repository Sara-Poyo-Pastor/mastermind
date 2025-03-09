<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use OpenApi\Annotations as OA;


class GameController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/games",
     *     summary="Lista todos los juegos",
     *     tags={"Games"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de juegos obtenida correctamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="status", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $games = Game::with('moves')->get();
        $games->each(function ($game) {
            $game->makeHidden('code');
        });
        return response()->json($games);
    }

    /**
     * @OA\Get(
     *     path="/api/games/{id}",
     *     summary="Obtiene un juego por su ID",
     *     tags={"Games"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del juego",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del juego",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Juego no encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $game = Game::with('moves')->findOrFail($id);
        $game->makeHidden('code');
        return response()->json($game);
    }

    /**
     * @OA\Post(
     *     path="/api/games",
     *     summary="Crea un nuevo juego",
     *     tags={"Games"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Mi Juego")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Juego creado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $name = $request->input('name', 'Unnamed Game');
        $code = $this->generateSecretCode();

        $game = Game::create([
            'name'   => $name,
            'code'   => $code,
            'status' => 'jugando...'
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

