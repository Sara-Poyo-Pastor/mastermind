<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Game;
use App\Models\Move;

class GameTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que se pueda crear una partida sin revelar el código secreto.
     */
    public function test_can_create_game()
    {
        $response = $this->json('POST', '/api/games', ['name' => 'Test Game']);

        $response->assertStatus(201)
            ->assertJsonMissing(['code'])
            ->assertJsonStructure([
                'id', 'name', 'status', 'created_at', 'updated_at'
            ]);

        $this->assertDatabaseHas('games', ['name' => 'Test Game']);
    }

    /**
     * Prueba que se pueda listar las partidas.
     */
    public function test_can_list_games()
    {
        Game::factory()->create();

        $response = $this->json('GET', '/api/games');

        $response->assertStatus(200)
            ->assertJsonStructure([
                ['id', 'name', 'status', 'created_at', 'updated_at']
            ]);
    }

    /**
     * Prueba que se pueda mostrar el detalle de una partida, incluyendo sus movimientos.
     */
    public function test_can_show_game()
    {
        $game = Game::factory()->create();

        $response = $this->json('GET', '/api/games/' . $game->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'name', 'status', 'moves', 'created_at', 'updated_at'
            ]);
    }

    /**
     * Prueba que se pueda enviar un movimiento correctamente y se evalúe la jugada.
     * Se utiliza una partida con un código secreto conocido para verificar que la evaluación
     * sea correcta (en este ejemplo, se asume que la jugada es idéntica al código secreto).
     */
    public function test_can_store_move()
    {
         // Crear una partida con un código secreto específico
        $secret = ['red', 'blue', 'green', 'yellow'];
        $game = Game::factory()->create([
            'code'   => $secret,
            'status' => 'in_progress'
        ]);

         // Enviar una jugada idéntica al código secreto
        $moveData = ['code' => $secret];

        $response = $this->json('POST', '/api/games/' . $game->id . '/move', $moveData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'move' => ['id', 'game_id', 'guessed_colors', 'evaluation', 'created_at'],
                'game_status', 'moves_remaining'
            ]);

         // Verificar que la evaluación sea 4 exactos y 0 parciales
        $json = $response->json();
        $this->assertEquals(4, $json['move']['evaluation']['exact']);
        $this->assertEquals(0, $json['move']['evaluation']['partial']);
    }

    /**
     * Prueba que se rechace el movimiento si se envían colores duplicados.
     */
    public function test_move_invalid_if_colors_duplicated()
    {
        $secret = ['red', 'blue', 'green', 'yellow'];
        $game = Game::factory()->create([
            'code'   => $secret,
            'status' => 'in_progress'
        ]);

         // Enviar una jugada con colores duplicados
        $moveData = ['code' => ['red', 'red', 'green', 'yellow']];
        $response = $this->json('POST', '/api/games/' . $game->id . '/move', $moveData);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'No se permiten colores duplicados en la jugada'
            ]);
    }
}
