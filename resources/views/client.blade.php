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
  
  <!-- Contenedor para seleccionar colores -->
  <div id="color-selector">
    <button type="button" class="color-btn" data-color="rojo" style="background-color: #ff6961;"></button>
    <button type="button" class="color-btn" data-color="azul" style="background-color: #84b6f4;"></button>
    <button type="button" class="color-btn" data-color="verde" style="background-color: #77dd77;"></button>
    <button type="button" class="color-btn" data-color="amarillo" style="background-color: #fdfd96;"></button>
    <button type="button" class="color-btn" data-color="naranja" style="background-color: #ffca99;"></button>
    <button type="button" class="color-btn" data-color="morado" style="background-color: #bc98f3;"></button>
  </div>
  
  <!-- Mostrar los colores seleccionados -->
  <p id="selected-colors">Colores seleccionados: </p>
  
  <!-- Botón para enviar la jugada -->
  <button id="play-move-btn">Enviar Movimiento</button>
  
  <div id="play-move-result" class="result"></div>
</section>

    <!-- <section id="play-move">
      <h2>Enviar Movimiento</h2>
      <input type="number" id="play-game-id" placeholder="ID de la partida">
      <input type="text" id="move-code" placeholder="Colores (ej. rojo, azul, verde, amarillo)">
      <button id="play-move-btn">Enviar Movimiento</button>
      <div id="play-move-result" class="result"></div>
    </section> -->
  </div>
</body>
</html>
