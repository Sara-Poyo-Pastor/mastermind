<?php
/**
 * Cliente CLI para jugar Mastermind contra la API.
 * 
 * Funcionalidades:
 * - Listar partidas en juego.
 * - Consultar detalles de una partida y sus jugadas.
 * - Crear una nueva partida.
 * - Jugar una partida (enviar un movimiento).
 * 
 * Para ejecutar este cliente, usa:
 *   php client.php
 */

// URL base de la API
$baseUrl = 'http://127.0.0.1:8000/api';

/**
 * Realiza una petición HTTP usando cURL.
 *
 * @param string $method Método HTTP (GET, POST, etc.).
 * @param string $url URL completa.
 * @param array|null $data Datos a enviar (opcional).
 * @return array [status code, response body]
 */
function makeRequest($method, $url, $data = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    if ($data !== null) {
        $payload = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    }
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch) . "\n";
    }
    curl_close($ch);
    return [$status, $response];
}

/**
 * Muestra un mensaje y lee la entrada del usuario.
 *
 * @param string $message
 * @return string
 */
function prompt($message) {
    echo $message;
    return trim(fgets(STDIN));
}

/**
 * Lista las partidas en juego.
 */
function listGames($baseUrl) {
    list($status, $response) = makeRequest('GET', $baseUrl . '/games');
    echo "Status: $status\n";
    echo "Response: $response\n";
}

/**
 * Consulta los detalles de una partida.
 *
 * @param string $baseUrl
 * @param int $gameId
 */
function showGame($baseUrl, $gameId) {
    list($status, $response) = makeRequest('GET', $baseUrl . '/games/' . $gameId);
    echo "Status: $status\n";
    echo "Response: $response\n";
}

/**
 * Crea una nueva partida.
 *
 * @param string $baseUrl
 * @param string $name
 */
function createGame($baseUrl, $name) {
    list($status, $response) = makeRequest('POST', $baseUrl . '/games', ['name' => $name]);
    echo "Status: $status\n";
    echo "Response: $response\n";
}

/**
 * Envía un movimiento para una partida.
 *
 * @param string $baseUrl
 * @param int $gameId
 * @param array $colors Arreglo de 4 colores.
 */
function playMove($baseUrl, $gameId, $colors) {
    list($status, $response) = makeRequest('POST', $baseUrl . '/games/' . $gameId . '/move', ['code' => $colors]);
    echo "Status: $status\n";
    echo "Response: $response\n";
}

// Menú interactivo
while (true) {
    echo "\n--- Cliente Mastermind ---\n";
    echo "1. Listar partidas en juego\n";
    echo "2. Consultar detalles de una partida\n";
    echo "3. Crear nueva partida\n";
    echo "4. Jugar una partida (enviar movimiento)\n";
    echo "5. Salir\n";
    $choice = prompt("Elige una opción: ");

    switch ($choice) {
        case 1:
            listGames($baseUrl);
            break;
        case 2:
            $gameId = prompt("Introduce el ID de la partida: ");
            showGame($baseUrl, $gameId);
            break;
        case 3:
            $name = prompt("Introduce el nombre de la nueva partida (opcional): ");
            createGame($baseUrl, $name);
            break;
        case 4:
            $gameId = prompt("Introduce el ID de la partida: ");
            echo "Introduce 4 colores separados por coma.\n";
            echo "Colores permitidos: rojo, azul, verde, amarillo, naranja, morado\n";
            $input = prompt("Colores: ");
            $colors = array_map('trim', explode(',', $input));
            if (count($colors) != 4) {
                echo "Debes introducir 4 colores.\n";
            } else {
                playMove($baseUrl, $gameId, $colors);
            }
            break;
        case 5:
            exit("Saliendo...\n");
        default:
            echo "Opción no válida.\n";
    }
}
