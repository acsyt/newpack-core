# NEWPACK-CORE

## 🚀 Primeros pasos
1. **Instalar dependencias:**
   ```bash
   composer install
   ```

2. **Copiar el archivo .env.example a .env y editar las credenciales de BD**

3. **Ejecutar**
   ```bash
   php artisan key:generate
   ```

4. **Configurar base de datos y migraciones:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Usuario de prueba:**
   - Email: `admin@acsyt.com`
   - Password: `123456`

6. **Iniciar servidor:**
   ```bash
   php artisan serve --port=8001
   ```

# New Pack Core

<<<<<<< HEAD
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

### Personalizar plantilla de modelo (stubs)

```bash
php artisan stub:publish
```

- Edita el archivo `stubs/model.stub` para adaptar tus modelos por defecto.
- Puedes agregar traits, propiedades y configuración común (p.ej. `$guarded`, `$casts`).
- Ejemplo mínimo de `model.stub`:
  ```php
  <?php

  namespace {{ namespace }};

  use Illuminate\Database\Eloquent\Model;

  class {{ class }} extends Model
  {
      // use HasCamelCaseAttributes;
      protected $guarded = [];
  }
  ```

- Para que se aplique al generar nuevos modelos:
  ```bash
  php artisan make:model Example -m
  ```

## 🏗️ Arquitectura de módulos
```
Controller
├── FormRequest (validación)
├── Resource (transformación de respuesta)
├── Actions (lógica de negocio)
├── Queries (consultas complejas)
└── Services (servicios externos)
```

## 📋 Consideraciones técnicas

### Actions sobre Services
Utilizar **Actions** para lógica de negocio que manipule datos (crear, actualizar, eliminar).

**¿Cómo usar un Action?**
- Inyección de dependencias en el constructor
- Inyección de método en el controlador

**Ejemplo:**
```php
public function store(Request $request, CreateUser $createUser)
{
    $user = $createUser->handle($request);
    return response()->json($user);
}
```

**Importante:** Siempre envolver las operaciones en `DB::transaction` cuando se trate de transacciones que afecten a más de una entidad.

### Jobs y Queues
**SIEMPRE USAR QUEUES** para operaciones que demoren tiempo:
- Cargas masivas
- Exportación de datos
- Procesamiento en segundo plano

### Arquitectura Query
Para endpoints con filtros, paginación y ordenamiento, usar `BaseQuery`.

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
=======
7. **Personalizar plantilla de modelo (stubs):**
   ```bash
   php artisan stub:publish
   ```

   - Edita el archivo `stubs/model.stub` para adaptar tus modelos por defecto.
   - Puedes agregar traits, propiedades y configuración común (p.ej. `$guarded`, `$casts`).
   - Ejemplo mínimo de `model.stub`:
     ```php
     <?php

     namespace {{ namespace }};

     use Illuminate\\Database\\Eloquent\\Model;

     class {{ class }} extends Model
     {
         // use HasCamelCaseAttributes;
         protected $guarded = [];
     }
     ```

   - Para que se aplique al generar nuevos modelos:
     ```bash
     php artisan make:model Example -m
     ```

## 🏗️ Arquitectura de módulos
```
Controller
├── FormRequest (validación)
├── Resource (transformación de respuesta)
├── Actions (lógica de negocio)
├── Queries (consultas complejas)
└── Services (servicios externos)
```

## 📋 Consideraciones técnicas

### Actions sobre Services
Utilizar **Actions** para lógica de negocio que manipule datos (crear, actualizar, eliminar).

**¿Cómo usar un Action?**
- Inyección de dependencias en el constructor
- Inyección de método en el controlador

**Ejemplo:**
```php
public function store(Request $request, CreateUser $createUser)
{
    $user = $createUser->handle($request);
    return response()->json($user);
}
```

**Importante:** Siempre envolver las operaciones en `DB::transaction` cuando se trate de transacciones que afecten a más de una entidad.

### Jobs y Queues
**SIEMPRE USAR QUEUES** para operaciones que demoren tiempo:
- Cargas masivas
- Exportación de datos  
- Procesamiento en segundo plano

### Arquitectura Query
Para endpoints con filtros, paginación y ordenamiento, usar `BaseQuery`.

## 📚 Librerías recomendadas
   
>>>>>>> 0e0c8b6327b82004f1087e18b6e9a7f0f567a483
