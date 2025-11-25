<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# New Pack Core

## 🚀 Instalación

### Opción 1: Instalación Automática (Recomendada)

```bash
git clone <repository-url>
cd newpack-core

composer setup
```

Este comando ejecutará automáticamente:
- `composer install`
- Creación del archivo `.env`
- `php artisan key:generate`
- `php artisan migrate --force`

### Opción 2: Instalación Manual

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd newpack-core
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Configurar variables de entorno**
```bash
cp .env.example .env
```

Edita el archivo `.env` y configura:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=newpack_core
DB_USERNAME=root
DB_PASSWORD=
```

4. **Generar clave de aplicación**
```bash
php artisan key:generate
```

5. **Ejecutar migraciones y seeders**
```bash
php artisan migrate:fresh --seed
```

6. **Generar documentación de Swagger**
```bash
php artisan l5-swagger:generate
```

## 💻 Desarrollo

### Iniciar entorno de desarrollo

```bash
composer dev
```

Este comando inicia simultáneamente:
- 🌐 **Servidor Laravel** (`php artisan serve`)
- 🔄 **Queue Worker** (`php artisan queue:listen`)
- 📋 **Logs en tiempo real** (`php artisan pail`)

### Comandos útiles

```bash
# Ejecutar migraciones
php artisan migrate

# Revertir última migración
php artisan migrate:rollback

# Refrescar base de datos con seeders
php artisan migrate:fresh --seed

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Generar documentación de Swagger
php artisan l5-swagger:generate
```

## 📚 Documentación API (Swagger)

Acceder a la documentación de la API:
```
http://localhost:8000/api/documentation
```

### Generar/Actualizar documentación

```bash
php artisan l5-swagger:generate
```

## 🔑 Credenciales por Defecto

Después de ejecutar los seeders, puedes acceder con:

```
Email: admin@acsyt.com
Password: 123456
```
