
document.addEventListener('DOMContentLoaded', function() {
 
  const baseUrl = window.apiBaseUrl;
  console.log("Usando baseUrl:", baseUrl);

  function formatGuessedColors(colors) {
    const colorMap = {
      "rojo": "#ff6961",
      "azul": "#84b6f4",
      "verde": "#77dd77",
      "amarillo": "#fdfd96",
      "naranja": "#ffca99",
      "morado": "#bc98f3",
    };

    return colors.map(color => {
      const hex = colorMap[color.toLowerCase()] || "#ccc";
      return `<span style="
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 1px solid #000;
        border-radius: 50%;
        margin-right: 5px;
        background-color: ${hex};
      " title="${color}"></span>`;
    }).join(' ');
  }

  //Formatea el resultado del movimiento de forma legible.

  function formatMoveResult(data, status) {
    if (!data || !data.move) {
      return `<p>Error: No se han recibido datos del movimiento.</p>`;
    }
    
    const evalResult = data.move.evaluation || {};
    const exact = evalResult.exact !== undefined ? evalResult.exact : 0;
    const partial = evalResult.partial !== undefined ? evalResult.partial : 0;
    
    const guessedColors = Array.isArray(data.move.guessed_colors)
      ? formatGuessedColors(data.move.guessed_colors)
      : data.move.guessed_colors;
    
    let html = `<h3>Resultado del Movimiento</h3>`;
    html += `<p><strong>Colores Propuestos:</strong> ${guessedColors}</p>`;
    html += `<p><strong>Colores en posición correcta:</strong> ${exact}</p>`;
    html += `<p><strong>Colores correctos pero en posición incorrecta:</strong> ${partial}</p>`;
    html += `<p><strong>Estado del Juego:</strong> ${data.game_status}</p>`;
    html += `<p><strong>Movimientos Restantes:</strong> ${data.moves_remaining}</p>`;
    return html;
  }

  /**
   * Formatea la lista de partidas de forma legible.
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

  // Variable para almacenar los colores seleccionados
  let selectedColors = [];
  
  // Asignar evento a cada botón de color
  const colorButtons = document.querySelectorAll('#color-selector .color-btn');
  colorButtons.forEach(button => {
    button.addEventListener('click', function() {
      const color = this.getAttribute('data-color');
      if (selectedColors.includes(color)) {
        // Si ya está seleccionado, quítalo
        selectedColors = selectedColors.filter(c => c !== color);
        this.classList.remove('selected');
      } else {
        if (selectedColors.length < 4) {
          selectedColors.push(color);
          this.classList.add('selected');
        } else {
          alert("Solo se pueden seleccionar 4 colores.");
        }
      }
      document.getElementById('selected-colors').innerText = "Colores seleccionados: " + selectedColors.join(', ');
    });
  });

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
            
            let guessedColors;
            if (Array.isArray(move.guessed_colors)) {
              guessedColors = formatGuessedColors(move.guessed_colors);
            } else {
              try {
                const parsedColors = JSON.parse(move.guessed_colors);
                if (Array.isArray(parsedColors)) {
                  guessedColors = formatGuessedColors(parsedColors);
                } else {
                  guessedColors = move.guessed_colors;
                }
              } catch(e) {
                guessedColors = move.guessed_colors;
              }
            }
            
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
    // Usamos el arreglo de colores seleccionados
    if (selectedColors.length !== 4) {
      alert("Por favor, selecciona 4 colores.");
      return;
    }
    fetch(`${baseUrl}/games/${gameId}/move`, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ code: selectedColors })
    })
    .then(res => res.json().then(data => ({ status: res.status, data })))
    .then(result => {
      const formatted = formatMoveResult(result.data, result.status);
      document.getElementById('play-move-result').innerHTML = formatted;
      
      // Mostrar modal personalizado si el juego ha terminado
      if(result.data.game_status === 'victory'){
        showModal("¡Felicidades!", "Has ganado la partida.");
      } else if(result.data.game_status === 'defeat'){
        showModal("¡Oh no!", "Has perdido la partida.");
      }
      
      // Resetear la selección
      selectedColors = [];
      document.getElementById('selected-colors').innerText = "Colores seleccionados: ";
      colorButtons.forEach(btn => btn.classList.remove('selected'));
    })
    .catch(err => console.error(err));
  });
});