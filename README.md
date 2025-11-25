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
   
