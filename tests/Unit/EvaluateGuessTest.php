<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Api\MoveController;

class EvaluateGuessTest extends TestCase
{
    /**
     * Prueba que el método evaluateGuess retorne el resultado correcto.
     */
    public function test_evaluate_guess()
    {
        // Crear una instancia del controlador
        $controller = new MoveController();

        // Acceder al método privado 'evaluateGuess' usando Reflection
        $reflection = new \ReflectionMethod($controller, 'evaluateGuess');
        $reflection->setAccessible(true);

        // Definir un código secreto y una jugada de ejemplo
        $secret = ['rojo', 'azul', 'verde', 'amarillo'];
        $guess  = ['rojo', 'verde', 'azul', 'amarillo'];

        // Invocar el método y obtener el resultado
        $result = $reflection->invokeArgs($controller, [$secret, $guess]);

        // En este ejemplo:
        // - Las posiciones 0 y 3 son exactas (red y yellow)
        // - Los elementos comunes totales son 4 (red, blue, green, yellow)
        // Por lo tanto, 'exact' debe ser 2 y 'partial' debe ser 4 - 2 = 2.
        $this->assertEquals(2, $result['exact']);
        $this->assertEquals(2, $result['partial']);
    }
}
