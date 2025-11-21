<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


# ERP Core

## Instalación

1. Navega al directorio del proyecto

2. Instala las dependencias del proyecto con Composer:
```
composer install
```

3. Crea un archivo `.env` basado en `.env.example` y actualiza la configuración de la base de datos según sea necesario.

4. Genera una clave de aplicación:
```
php artisan key:generate
```

5. Ejecuta las migraciones de la base de datos con los datos de prueba:
```
php artisan migrate:fresh --seed
```

6. Inicia el servidor de desarrollo de Laravel:
```
php artisan serve
```

El servidor de desarrollo estará disponible en `http://localhost:8000`.

