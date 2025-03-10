# README.md

## Requisitos Previos

- **Servidor Web:** MySQL  
- **Sistema Operativo:** Windows (o similar)  
- **PHP:** Versión 8.x (preferiblemente 8.1 o superior)  
- **Base de Datos:** MySQL  
- **Composer:** Para la gestión de dependencias  
- **Git:** (Opcional) Para clonar el repositorio

## Pasos para la Instalación

1. Clona el repositorio o extrae el archivo comprimido en tu servidor. https://github.com/Sara-Poyo-Pastor/mastermind.git
2. Ejecuta el comando `composer install` para instalar las dependencias.
3. En el archivo`.env` configura la conexión a la base de datos.
4. Ejecuta las migraciones para crear las tablas necesarias:  
   `php artisan migrate`
5. (Opcional) Ejecuta los tests para verificar la funcionalidad:  
   `php artisan test`
6. Inicia el servidor de desarrollo:  
   `php artisan serve`
7. Inicia el servidor para cliente:
   `npm run dev`   

La aplicación estará disponible en [http://127.0.0.1:8000](http://localhost:8000).


# Documentación de la API Mastermind

Esta API permite gestionar partidas y jugadas para el juego Mastermind.
Se adjunta un documento en formato json (Mastermind-API.postman_collection.json) con la documentación completa de la API.
A su vez, se ha implementado Swagger como otra manera alternativa para documentar la API. Se puede acceder a ella a través de http://localhost:8000/documentacion

## Endpoints Principales

### 1. Listar Partidas
- **Método:** GET  
- **URL:** `{{baseUrl}}/api/games`  
- **Descripción:** Devuelve una lista de partidas en curso (el código secreto se oculta).

### 2. Detalle de una Partida
- **Método:** GET  
- **URL:** `{{baseUrl}}/api/games/{{id}}`  
- **Descripción:** Devuelve los detalles de una partida, incluyendo los movimientos realizados.

### 3. Crear una Nueva Partida
- **Método:** POST  
- **URL:** `{{baseUrl}}/api/games`  
- **Body (JSON):**
  ```json
  {
      "name": "Partida de Prueba"
  }

### 4. Enviar un Movimiento

- **Método:** `POST`
- **URL:** `{{baseUrl}}/api/games/{{gameId}}/move`
- **Descripción:** Envía una jugada. El servidor evalúa la jugada, actualiza el estado de la partida (se marca como "victory" si se acierta la jugada o "defeat" si se alcanzan 10 intentos) y devuelve el resultado, indicando el número de aciertos exactos y parciales, así como la cantidad de jugadas restantes.
- **Body (JSON):** 
  ```json
  {
      "code": ["rojo", "azul", "verde", "amarillo"]
  }
