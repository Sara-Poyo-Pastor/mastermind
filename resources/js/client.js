// Usa la variable global definida en la vista
// Asegúrate de que en tu vista Blade hayas definido window.apiBaseUrl
const baseUrl = window.apiBaseUrl;
console.log("Usando baseUrl:", baseUrl);

/**
 * Función auxiliar para formatear y mostrar el resultado de un movimiento de forma legible.
 * @param {object} data - Los datos del movimiento devueltos por la API.
 * @param {number} status - El código de estado HTTP de la respuesta.
 * @returns {string} HTML formateado.
 */
function formatMoveResult(data) {
    // Verificamos que existan los datos esperados
    if (!data || !data.move) {
      return `<p>Error: No se han recibido datos del movimiento.</p>`;
    }
  
    // Extraer la evaluación
    // Se espera que la API devuelva la evaluación con las claves 'positioned_colors' y 'non_positioned_colors'
    const evalResult = data.move.evaluation || {};
    const positioned = evalResult.exact !== undefined ? evalResult.exact : 0;
    const nonPositioned = evalResult.partial !== undefined ? evalResult.partial : 0;
  
    let html = `<h3>Resultado del Movimiento</h3>`;
    html += `<p><strong>Colores en posición correcta:</strong> ${positioned}</p>`;
    html += `<p><strong>Colores correctos pero en posición incorrecta:</strong> ${nonPositioned}</p>`;
    html += `<p><strong>Estado del Juego:</strong> ${data.game_status}</p>`;
    html += `<p><strong>Movimientos Restantes:</strong> ${data.moves_remaining}</p>`;
    return html;
  }

/**
 * Función auxiliar para formatear la lista de partidas.
 * @param {array} games - Array de partidas.
 * @returns {string} HTML formateado.
 */
function formatGamesList(games) {
  if (!Array.isArray(games)) {
    return `<p>Error: datos inválidos.</p>`;
  }
  let html = `<h3>Lista de Partidas</h3>`;
  html += `<ul>`;
  games.forEach(game => {
    html += `<li>ID: ${game.id} - Nombre: ${game.name} - Estado: ${game.status} - Creada: ${game.created_at}</li>`;
  });
  html += `</ul>`;
  return html;
}


// Crear Partida
document.getElementById('create-game-btn').addEventListener('click', function(){
  const name = document.getElementById('game-name').value || 'Unnamed Game';
  fetch(`${baseUrl}/games`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ name })
  })
  .then(res => res.json().then(data => ({ status: res.status, data })))
  .then(result => {
    // Mostrar de forma legible el resultado de la creación de la partida
    document.getElementById('create-game-result').innerHTML = `<p><strong>Partida creada con ID:</strong> ${result.data.id} - Estado: ${result.data.status}</p>`;
  })
  .catch(err => console.error(err));
});

// Listar Partidas
document.getElementById('list-games-btn').addEventListener('click', function(){
  fetch(`${baseUrl}/games`)
    .then(res => res.json())
    .then(data => {
      document.getElementById('games-list').innerHTML = formatGamesList(data);
    })
    .catch(err => console.error(err));
});

// Mostrar Detalles de una Partida
document.getElementById('show-game-btn').addEventListener('click', function(){
    const gameId = document.getElementById('game-id').value;
    fetch(`${baseUrl}/games/${gameId}`)
      .then(res => res.json())
      .then(data => {
        let html = `<h3>Detalle de la Partida</h3>`;
        html += `<p><strong>ID:</strong> ${data.id}</p>`;
        html += `<p><strong>Nombre:</strong> ${data.name}</p>`;
        html += `<p><strong>Estado:</strong> ${data.status}</p>`;
        html += `<p><strong>Creada:</strong> ${data.created_at}</p>`;
        
        if (data.moves && data.moves.length > 0) {
          html += `<h4>Movimientos Realizados:</h4>`;
          html += `<ul>`;
          data.moves.forEach(move => {
            // Procesar evaluación: si es string, parsearlo; si ya es objeto, usarlo directamente.
            let evalResult = move.evaluation;
            if (typeof evalResult === 'string') {
              try {
                evalResult = JSON.parse(evalResult);
              } catch(e) {
                evalResult = {};
              }
            }
            const exact = evalResult.exact !== undefined ? evalResult.exact : 0;
            const partial = evalResult.partial !== undefined ? evalResult.partial : 0;
            
            // Procesar colores propuestos
            let guessedColors = Array.isArray(move.guessed_colors) 
                ? move.guessed_colors.join(', ') 
                : move.guessed_colors;
            
            html += `<li>`;
            html += `<strong>Colores Propuestos:</strong> ${guessedColors}<br>`;
            html += `<strong>Evaluación:</strong> ${exact} posicionados, ${partial} no posicionados`;
            html += `</li>`;
          });
          html += `</ul>`;
        } else {
          html += `<p>No se han realizado movimientos.</p>`;
        }
        document.getElementById('game-detail-result').innerHTML = html;
      })
      .catch(err => console.error(err));
});

  
  
  
  
  

// Enviar Movimiento
document.getElementById('play-move-btn').addEventListener('click', function(){
    const gameId = document.getElementById('play-game-id').value;
    const moveCodeInput = document.getElementById('move-code').value;
    // Se espera que el usuario ingrese colores separados por comas.
    const code = moveCodeInput.split(',').map(c => c.trim()).filter(c => c !== '');
    if (code.length !== 4) {
        alert("Por favor, ingresa 4 colores separados por comas.");
        return;
    }
    fetch(`${baseUrl}/games/${gameId}/move`, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ code })
    })
    .then(res => res.json().then(data => ({ status: res.status, data })))
    .then(result => {
      const formatted = formatMoveResult(result.data, result.status);
      document.getElementById('play-move-result').innerHTML = formatted;
      
      // Mostrar un popup en función del estado del juego
      if(result.data.game_status === 'victory'){
        alert("¡Felicidades! Has ganado la partida!");
      } else if(result.data.game_status === 'defeat'){
        alert("Lo siento, has perdido la partida.");
      }
    })
    .catch(err => console.error(err));
  });
  
