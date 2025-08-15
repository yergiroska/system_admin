# Módulo de Compañías

Este documento describe el módulo de Compañías: estructura del modelo, relaciones con productos, rutas, vistas y comportamiento del controlador.

## Modelo: App\Models\Company
- Tabla: companies
- SoftDeletes: Sí (deleted_at)
- Fillable: name, description
- Métodos de acceso:
  - getId(), getName(), getDescription()
  - setName($name), setDescription($description)
- Relaciones:
  - products(): BelongsToMany(App\Models\Product)
    - Usando modelo pivote App\Models\CompanyProduct
    - Alias del pivote: `companyProduct`
    - withPivot(['id','price']): el pivote tiene su propio id y columna price
    - withTimestamps(): el pivote gestiona created_at/updated_at

## Modelo pivote: App\Models\CompanyProduct
- Tabla: company_product
- Primary key: id (autoincremental)
- Timestamps: true
- Fillable: id, company_id, product_id, price
- Getters: getId(), getCompanyId(), getProductId(), getPrice()

Este pivote permite:
- Asignar precio por producto y por compañía.
- Identificar unívocamente la relación mediante `company_product.id` (importante para compras).

## Controlador: App\Http\Controllers\CompanyController
Operaciones principales:
- index(): Lista compañías (vista: companies.index). Requiere auth (middleware aplicado en __construct).
- create(): Formulario de creación con listado de productos (vista: companies.create).
- store(Request): Valida (name único, description requerida), crea compañía, asocia productos (attach), registra Log (CREAR). Respuesta JSON.
- show($id): Muestra detalles de una compañía (vista: companies.show).
- edit($id): Formulario de edición con productos (vista: companies.edit).
- update($id, Request): Valida, actualiza y sincroniza productos (sync). Respuesta JSON.
- destroy($id): Registra Log (ELIMINAR) y elimina. Respuesta JSON.
- viewCompanies(): Vista general (companies.view_companies).
- listCompanies(): Retorna JSON con [{id, name, description, url_detail}].

Validaciones:
- store: name required|unique:companies,name; description required.
- update: name required; description required.

Logs: registra action, objeto, objeto_id, detail, ip, user_id en crear/eliminar.

## Rutas (routes/web.php)
Prefijo: /companies (name: companies., middleware: auth)
- GET  /companies             -> companies.index
- GET  /companies/create      -> companies.create
- GET  /companies/{id}/edit   -> companies.edit
- POST /companies/save        -> companies.store (JSON)
- PUT  /companies/{id}/update -> companies.update (JSON)
- DELETE /companies/{id}/delete -> companies.destroy (JSON)
- GET  /companies/view        -> companies.view
- GET  /companies/list        -> companies.lists (JSON)
- GET  /companies/{id}/show   -> companies.show

## Vistas y componentes relevantes
- companies.index / create / edit / show / view_companies
- resources/views/inc/products.blade.php: listado de productos con checkboxes y campo de precio por producto.
  - Si el producto ya está asociado a la compañía, el checkbox aparece seleccionado y el input de precio habilitado y precargado.
  - El valor del precio se obtiene de `$product->companyProduct->price` para el par (company, product).

## Consideraciones
- Autenticación obligatoria via middleware en todas las rutas del módulo.
- Gestión de precios por producto usando el pivote: recuerde persistir/actualizar `price` al adjuntar/sincronizar si el formulario lo envía.
- La API listCompanies entrega además `url_detail` útil para construir enlaces hacia companies.show.
