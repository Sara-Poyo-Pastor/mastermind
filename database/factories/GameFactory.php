<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Game;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition()
    {
        // Genera un código secreto aleatorio de 4 colores
        $colors = ['rojo', 'azul', 'verde', 'amarillo', 'naranja', 'morado'];
        shuffle($colors);
        $code = array_slice($colors, 0, 4);

        return [
            'name'   => $this->faker->word,
            'code'   => $code,
            'status' => 'in_progress',
        ];
    }
}

