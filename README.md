<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


# ERP Core


## Instalación

1. Clona el repositorio en tu máquina local:
```
git clone https://devacsyt@bitbucket.org/paletstudio/newpack-core.git
```

2. Navega hasta el directorio del proyecto:
```
cd newpack-core
```

3. Instala las dependencias del proyecto utilizando Composer:
```
composer install
```

4. Crea un archivo `.env` basado en el archivo `.env.example` y actualiza la configuración de la base de datos y otros ajustes según sea necesario.

5. Genera una clave de aplicación:
```
php artisan key:generate
```

6. Ejecuta las migraciones de la base de datos con el seeder incluido:
```
php artisan migrate:fresh --seed
```

7. Inicia el servidor de desarrollo de Laravel:
```
php artisan serve
```

El servidor de desarrollo se ejecutará en `http://localhost:8000`.

# Purgar la bd y correr seed de los tenant
```
php artisan tenants:migrate-fresh
php artisan tenants:seed
```


# Iniciar un tenant en tinker
```
tenancy()->initialize( Tenant::first() );
```
