<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Api\MoveController;

class EvaluateGuessTest extends TestCase
{
    //Prueba que el método evaluateGuess retorne el resultado correcto.
    public function test_evaluate_guess()
    {
        $controller = new MoveController();

        $reflection = new \ReflectionMethod($controller, 'evaluateGuess');
        $reflection->setAccessible(true);

        $secret = ['rojo', 'azul', 'verde', 'amarillo'];
        $guess  = ['rojo', 'morado', 'verde', 'amarillo'];

        $result = $reflection->invokeArgs($controller, [$secret, $guess]);

        $this->assertEquals(3, $result['exact']);
        $this->assertEquals(0, $result['partial']);
    }
}
