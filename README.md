# README.md

## Requisitos

- PHP >= 7.4
- Composer
- MySQL o MariaDB
- Apache (u otro servidor web compatible con PHP)

## Pasos para la Instalación

1. Clona el repositorio o extrae el archivo comprimido en tu servidor.
2. Ejecuta el comando `composer install` para instalar las dependencias.
3. Copia el archivo `.env.example` a `.env` y configura la conexión a la base de datos.
4. Genera la clave de la aplicación con:  
   `php artisan key:generate`
5. Ejecuta las migraciones para crear las tablas necesarias:  
   `php artisan migrate`
6. (Opcional) Ejecuta los tests para verificar la funcionalidad:  
   `php artisan test`
7. Inicia el servidor de desarrollo:  
   `php artisan serve`

La aplicación estará disponible en [http://localhost:8000](http://localhost:8000).

## Notas Adicionales

- Si se requieren configuraciones especiales para el entorno LAMP, inclúyelas en este archivo.
- Para lanzar la aplicación en producción, configura el servidor Apache con el VirtualHost correspondiente y ajusta las variables de entorno en el archivo `.env`.

