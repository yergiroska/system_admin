# Documentación del Módulo de Compañía

## Introducción
Este documento proporciona información detallada sobre el módulo de compañía implementado en el sistema. El módulo permite la gestión completa de empresas, incluyendo la creación, visualización, edición y eliminación de registros de compañías, así como la asociación con productos.

## Modelo (Model)

### Archivo: `app/Models/Company.php`

El modelo `Company` representa la entidad de compañía en el sistema y tiene las siguientes características:

- **Tabla**: companies (implícita por convención de Laravel)
- **Características principales**:
  - Utiliza SoftDeletes para eliminación lógica
  - Campos rellenables (fillable): name, description, deleted_at
  - Campos ocultos (hidden): birth_date
  - Campos calculados (appends): formatted_birth_date

- **Relaciones**:
  - Relación muchos a muchos con el modelo Product a través de la tabla pivote company_product

```php
public function products(): BelongsToMany
{
    return $this->belongsToMany(Product::class);
}
```

## Controlador (Controller)

### Archivo: `app/Http/Controllers/CompanyController.php`

El controlador `CompanyController` gestiona todas las operaciones relacionadas con las compañías:

- **Middleware**: Requiere autenticación para todas las acciones
- **Métodos principales**:
  - `index()`: Muestra todas las compañías
  - `create()`: Muestra el formulario para crear una nueva compañía
  - `store()`: Guarda una nueva compañía en la base de datos
  - `show()`: Muestra los detalles de una compañía específica
  - `edit()`: Muestra el formulario para editar una compañía existente
  - `update()`: Actualiza una compañía existente
  - `destroy()`: Elimina una compañía (soft delete)
  - `viewCompanies()`: Muestra la vista para listar compañías
  - `listCompanies()`: Devuelve un JSON con todas las compañías

- **Características adicionales**:
  - Registra logs de las acciones realizadas (crear, actualizar, eliminar)
  - Gestiona las relaciones con productos

## Vistas (Views)

### Vistas principales:

1. **`resources/views/companies/index.blade.php`**: Lista todas las compañías
2. **`resources/views/companies/create.blade.php`**: Formulario para crear una nueva compañía
   - Incluye campos para nombre y descripción
   - Permite seleccionar productos relacionados
   - Utiliza AJAX para enviar el formulario

3. **`resources/views/companies/edit.blade.php`**: Formulario para editar una compañía existente
   - Similar al formulario de creación pero con datos precargados
   - Utiliza AJAX para enviar el formulario

4. **`resources/views/companies/show.blade.php`**: Muestra los detalles de una compañía específica

### Vistas parciales:

1. **`resources/views/inc/companies.blade.php`**: Componente reutilizable que muestra una lista de compañías como checkboxes
   - Utilizado en formularios de productos para establecer relaciones

## Rutas (Routes)

### Archivo: `routes/web.php`

Las rutas del módulo de compañía están agrupadas bajo el prefijo 'companies' y el nombre 'companies.':

```php
Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/list', [CompanyController::class, 'listCompanies'])->name('lists');
    Route::get('/create', [CompanyController::class, 'create'])->name('create');
    Route::post('/save', [CompanyController::class, 'store'])->name('store');
    Route::get('/view', [CompanyController::class, 'viewCompanies'])->name('view.companies');
    Route::get('/{id}/edit', [CompanyController::class, 'edit'])->name('edit');
    Route::put('/{id}', [CompanyController::class, 'update'])->name('update');
    Route::delete('/{id}/delete', [CompanyController::class, 'destroy'])->name('destroy');
    Route::get('/{id}', [CompanyController::class, 'show'])->name('show');
});
```

## Relaciones con otros módulos

### Relación con Productos:

- Existe una relación muchos a muchos entre compañías y productos
- La relación se gestiona a través de una tabla pivote (company_product)
- En el modelo Product:
  ```php
  public function companies(): BelongsToMany
  {
      return $this->belongsToMany(Company::class);
  }
  ```

## Flujo de trabajo típico

1. **Listar compañías**: Acceder a `/companies` para ver todas las compañías
2. **Crear compañía**: 
   - Acceder a `/companies/create`
   - Completar el formulario con nombre, descripción y productos relacionados
   - Enviar el formulario mediante AJAX
3. **Editar compañía**:
   - Acceder a `/companies/{id}/edit`
   - Modificar los campos necesarios
   - Actualizar mediante AJAX
4. **Eliminar compañía**:
   - Desde la lista de compañías, seleccionar eliminar
   - La eliminación es lógica (soft delete) manteniendo el registro en la base de datos

## Consideraciones técnicas

- El módulo utiliza SoftDeletes para la eliminación lógica de registros
- Se registran logs de las acciones realizadas en las compañías
- Las operaciones CRUD se realizan mediante AJAX para mejorar la experiencia de usuario
- La autenticación es requerida para todas las acciones del módulo
