# Documentación del Módulo de Productos

## Descripción General
El módulo de productos permite la gestión completa de productos en el sistema, incluyendo la creación, visualización, edición y eliminación de productos. Implementa una relación bidireccional muchos a muchos con el módulo de empresas, permitiendo asociar productos con empresas y viceversa. Esta integración facilita la gestión de catálogos de productos por empresa y la asignación de múltiples productos a diferentes empresas.

## Componentes del Módulo

### 1. Modelo (Product.php)
El modelo Product define la estructura de datos y relaciones para los productos:

- **Tabla**: products
- **Clave primaria**: id
- **Campos principales**: 
  - name (nombre del producto)
  - description (descripción del producto)
  - deleted_at (para borrado lógico)
- **Relaciones**:
  - Relación muchos a muchos con el modelo Company (empresas) mediante el método `companies()`
  - Utiliza `BelongsToMany` para definir la relación bidireccional
- **Características especiales**:
  - Implementa SoftDeletes para borrado lógico
  - Incluye un campo calculado para formatear fechas
  - Protege campos sensibles con `$hidden`
  - Agrega campos calculados con `$appends`

### 2. Controlador (ProductController.php)
El controlador gestiona todas las operaciones CRUD y la lógica de negocio:

- **Métodos principales**:
  - `index()`: Muestra la lista de todos los productos
  - `create()`: Muestra el formulario para crear un nuevo producto y carga todas las empresas disponibles
  - `store()`: Guarda un nuevo producto en la base de datos y establece sus relaciones con empresas
  - `edit()`: Muestra el formulario para editar un producto existente con sus empresas asociadas
  - `update()`: Actualiza un producto existente y sincroniza sus relaciones con empresas
  - `destroy()`: Elimina un producto lógicamente (soft delete)
  - `viewProducts()`: Vista especial para visualizar productos
  - `listProducts()`: Devuelve lista de productos en formato JSON para consumo por AJAX

- **Características especiales**:
  - Implementa validación de datos para nombre y descripción
  - Registra logs de actividad para creación y eliminación con detalles en formato JSON
  - Gestiona relaciones con empresas mediante los métodos `attach()` y `sync()`
  - Implementa respuestas JSON para operaciones AJAX
  - Requiere autenticación mediante middleware

### 3. Vistas
El módulo incluye varias vistas para la interfaz de usuario:

- **index.blade.php**: 
  - Muestra la lista de productos en formato tabla
  - Incluye enlaces para crear, editar y eliminar productos
  - Implementa eliminación mediante AJAX

- **create.blade.php**:
  - Formulario para crear nuevos productos
  - Incluye campos para nombre y descripción
  - Permite seleccionar empresas asociadas mediante checkboxes
  - Implementa envío mediante AJAX

- **edit.blade.php**:
  - Formulario para editar productos existentes
  - Muestra datos actuales del producto
  - Permite modificar empresas asociadas
  - Implementa actualización mediante AJAX

- **Componente companies.blade.php**:
  - Componente reutilizable para seleccionar empresas
  - Muestra checkboxes para cada empresa disponible
  - Marca automáticamente las empresas ya asociadas al producto
  - Utiliza la directiva `@checked` de Laravel para marcar las opciones seleccionadas

- **Componente products.blade.php**:
  - Componente reutilizable para seleccionar productos en formularios de empresas
  - Muestra checkboxes para cada producto disponible
  - Marca automáticamente los productos ya asociados a la empresa
  - Utiliza la misma estructura que companies.blade.php para mantener consistencia

### 4. Rutas
Las rutas definen los endpoints para acceder a las funcionalidades:

- **Prefijo**: /products
- **Nombre**: products
- **Rutas principales**:
  - GET /products: Lista todos los productos
  - GET /products/create: Muestra formulario de creación
  - POST /products/save: Guarda un nuevo producto
  - GET /products/{id}/edit: Muestra formulario de edición
  - PUT /products/{id}: Actualiza un producto
  - DELETE /products/{id}/delete: Elimina un producto
  - GET /products/view: Vista especial de productos
  - GET /products/list: Devuelve lista en formato JSON

## Relaciones
- **Productos-Empresas**: Relación muchos a muchos bidireccional implementada mediante una tabla pivote
  - Un producto puede estar asociado a múltiples empresas
  - Una empresa puede tener múltiples productos
  - La relación está implementada en ambos modelos:
    - En `Product.php`: Método `companies()` que define la relación con empresas
      ```php
      public function companies(): BelongsToMany
      {
          return $this->belongsToMany(Company::class);
      }
      ```
    - En `Company.php`: Método `products()` que define la relación con productos
      ```php
      public function products(): BelongsToMany
      {
          return $this->belongsToMany(Product::class);
      }
      ```
  - La tabla pivote `company_product` se crea automáticamente por Laravel siguiendo la convención de nombres (orden alfabético de los modelos)
  - La gestión de esta relación se realiza mediante los métodos:
    - `attach()`: Para asociar empresas a un nuevo producto o productos a una nueva empresa
      ```php
      // En ProductController.php - método store()
      $product->companies()->attach($request->companies ?? []);
      
      // En CompanyController.php - método store()
      $company->products()->attach($request->products ?? []);
      ```
    - `sync()`: Para actualizar las empresas asociadas a un producto o los productos asociados a una empresa
      ```php
      // En ProductController.php - método update()
      $product->companies()->sync($request->companies ?? []);
      
      // En CompanyController.php - método update()
      $company->products()->sync($request->products ?? []);
      ```
  - Componentes de vista para gestionar estas relaciones:
    - `companies.blade.php`: Permite seleccionar empresas en formularios de productos
    - `products.blade.php`: Permite seleccionar productos en formularios de empresas

## Funcionalidades AJAX
El módulo implementa operaciones asíncronas para mejorar la experiencia de usuario:
- Creación de productos sin recargar la página
- Actualización de productos sin recargar la página
- Eliminación de productos con confirmación y actualización dinámica de la interfaz
