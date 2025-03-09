<?php

namespace App\Http\Controllers\Api;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="API de Juegos",
 *         version="1.0.0",
 *         description="Documentación de la API para la gestión de juegos y movimientos",
 *         @OA\Contact(
 *             email="soporte@ejemplo.com"
 *         )
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8000",
 *         description="Servidor local"
 *     )
 * )
 */
class SwaggerController
{
    // Este controlador solo se usa para centralizar la documentación
}
