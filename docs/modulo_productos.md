# Módulo de Productos

Este documento describe el módulo de Productos: modelo, relaciones con compañías, rutas, vistas y comportamiento del controlador.

## Modelo: App\Models\Product
- Tabla: products
- SoftDeletes: Sí (deleted_at)
- Fillable: name, description
- Métodos de acceso:
  - getId(), getName(), getDescription()
  - setName($name), setDescription($description)
- Relaciones:
  - companies(): BelongsToMany(App\Models\Company)
    - using(App\Models\CompanyProduct)
    - as('companyProduct') — alias para el pivote
    - withPivot(['id','price']) — el pivote trae su id y el precio por compañía
    - withTimestamps()

## Pivote: App\Models\CompanyProduct (resumen)
- Tabla: company_product
- Columnas clave: id (PK autoincremental), company_id, product_id, price, timestamps
- Getters: getId(), getCompanyId(), getProductId(), getPrice()

## Controlador: App\Http\Controllers\ProductController
Operaciones principales:
- index(): Lista productos (vista: products.index).
- create(): Formulario de creación con listado de compañías (vista: products.create).
- store(Request): Valida y crea producto; asocia compañías (attach); registra Log (CREAR). JSON.
- edit($id): Formulario de edición con compañías (vista: products.edit).
- update($id, Request): Valida, actualiza y sincroniza compañías (sync). JSON.
- destroy($id): Registra Log (ELIMINAR) y elimina. JSON.
- viewProducts(): Vista general (products.view_products).
- listProducts(): API JSON {status, data} con todos los productos.

Validaciones:
- store/update: name required; description required.

Logs: se registran en crear/eliminar con action, objeto, objeto_id, detail, ip, user_id.

## Rutas (routes/web.php)
Prefijo: /products (name: products., middleware: auth)
- GET  /products             -> products.index
- GET  /products/create      -> products.create
- GET  /products/{id}/edit   -> products.edit
- POST /products/save        -> products.store (JSON)
- PUT  /products/{id}/update -> products.update (JSON)
- DELETE /products/{id}/delete -> products.destroy (JSON)
- GET  /products/view        -> products.view
- GET  /products/list        -> products.lists (JSON)

## Vistas y componentes relevantes
- products.index / create / edit / view_products
- Componente products de compañías: resources/views/inc/products.blade.php
  - Lista todos los productos con checkbox y campo de precio por producto (por compañía).
  - El precio proviene de `$company->products->firstWhere('id', $product->id)?->companyProduct?->price`.

## Consideraciones
- Asociación con compañías mediante tabla pivote con precio. Asegúrese de leer/escribir `price` junto con el attach/sync si el formulario lo envía.
- Autenticación obligatoria (middleware) para rutas del módulo.
- Las respuestas JSON de store/update/destroy siguen el formato {status: "success", message: "..."}.
