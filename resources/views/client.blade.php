<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cliente Mastermind</title>
  <!-- Define la variable global antes de cargar los assets -->
  <script>
    window.apiBaseUrl = "{{ url('api') }}";
    console.log("apiBaseUrl:", window.apiBaseUrl);
  </script>
  @vite(['resources/css/client.css', 'resources/js/client.js'])
</head>
<body>
  <div class="container">
    <h1>Cliente Mastermind</h1>
    
    <!-- Sección para Crear Partida -->
    <section id="create-game">
      <h2>Crear Partida</h2>
      <input type="text" id="game-name" placeholder="Nombre de la partida">
      <button id="create-game-btn">Crear Partida</button>
      <div id="create-game-result" class="result"></div>
    </section>
    
    <!-- Sección para Listar Partidas -->
    <section id="list-games">
      <h2>Listar Partidas</h2>
      <button id="list-games-btn">Listar Partidas</button>
      <div id="games-list" class="result"></div>
    </section>
    
    <!-- Sección para Consultar Detalles de una Partida -->
    <section id="game-details">
      <h2>Detalles de una Partida</h2>
      <input type="number" id="game-id" placeholder="ID de la partida">
      <button id="show-game-btn">Mostrar Partida</button>
      <div id="game-detail-result" class="result"></div>
    </section>
    
    <!-- Sección para Enviar Movimiento -->
    <section id="play-move">
      <h2>Enviar Movimiento</h2>
      <input type="number" id="play-game-id" placeholder="ID de la partida">
      <input type="text" id="move-code" placeholder="Colores (ej. rojo, azul, verde, amarillo)">
      <button id="play-move-btn">Enviar Movimiento</button>
      <div id="play-move-result" class="result"></div>
    </section>
  </div>
</body>
</html>
