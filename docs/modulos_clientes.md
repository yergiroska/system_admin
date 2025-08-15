# Módulo de Clientes

Este documento describe el módulo de Clientes del sistema: campos del modelo, relaciones, rutas principales, vistas y flujo de compra de productos por cliente.

## Modelo: App\Models\Customer
- Tabla: customers
- SoftDeletes: Sí (deleted_at)
- Fillable: first_name, last_name, birth_date, identity_document
- Ocultos: birth_date (se expone `formatted_birth_date`)
- Appends: formatted_birth_date
- Accessors/Mutators útiles:
  - getFullName(): Nombre completo con capitalización.
  - getFormattedBirthDateAttribute(): "dd-mm-YYYY".
- Relaciones:
  - purchases(): HasMany(App\Models\Purchase) — compras del cliente.
  - user_id: existe migración que agrega clave foránea nullable y única hacia users.id (onDelete cascade). Aún no hay relación definida en el modelo, pero el campo existe en BD.

## Migración relevante
- 2025_08_14_053149_add_user_id_to_customers_table.php
  - Agrega columna `user_id` (nullable, unique) referenciando `users(id)` con onDelete('cascade').

## Controlador: App\Http\Controllers\CustomerController
Operaciones principales:
- index(): Lista de clientes (vista: customers.index). Requiere usuario autenticado; si no, redirige a login.
- create(): Formulario de creación (vista: customers.create).
- store(Request): Valida y crea cliente; registra Log (CREAR). Respuesta JSON: {status, message}.
- edit($id): Formulario de edición (vista: customers.edit).
- update($id, Request): Valida y actualiza cliente. Respuesta JSON.
- destroy($id): Elimina cliente; registra Log (ELIMINAR). Respuesta JSON.
- viewCustomers(): Vista general (vista: customers.view_customers).
- listCustomers(): API JSON con todos los clientes {status, data}.
- getProducts(int $id): Carga vista para seleccionar productos agrupados por compañía (vista: customers.products).
- buy(int $id, Request): Registra compras a partir de IDs de la tabla pivote company_product; guarda vía relación purchases(). Redirige back con mensaje de estado.

Validaciones (store/update):
- first_name: required
- last_name: required
- birth_date: required|date
- identity_document: required|unique:customers (o unique con excepción en update)

Logs: se registran en acciones de crear y eliminar; se almacena action, objeto, objeto_id, detail, ip, user_id.

## Rutas (routes/web.php)
Prefijo: /customers (name: customers., middleware: auth para la mayoría)
- GET  /customers                -> customers.index          (listar)
- GET  /customers/create         -> customers.create         (form crear)
- GET  /customers/{id}/edit      -> customers.edit           (form editar)
- GET  /customers/{id}/get-products -> customers.get_products (UI comprar productos)
- POST /customers/save           -> customers.store          (crear, JSON)
- PUT  /customers/{id}/update    -> customers.update         (actualizar, JSON)
- DELETE /customers/{id}/delete  -> customers.destroy        (eliminar, JSON)
- POST /customers/{id}/buy       -> customers.buy            (registrar compra)
- GET  /customers/view           -> customers.view           (vista)
- GET  /customers/list           -> customers.lists          (API JSON)

## Vistas clave
- customers.index: listado de clientes.
- customers.create / customers.edit: formularios de alta/edición.
- customers.view_customers: vista de presentación.
- customers.products: selección de productos por compañía para un cliente en particular.
  - Renderiza compañías con sus productos y precios desde el pivote `company_product`.
  - El checkbox de cada producto envía el `company_product.id` (no el product.id).

## Flujo: compra de productos
1) Ir a GET /customers/{id}/get-products.
2) Seleccionar productos (cada item representa un registro en company_product con precio mostrado).
3) Enviar formulario a POST /customers/{id}/buy.
4) El controlador crea registros Purchase con `company_product_id` y los asocia al cliente.

## Notas y consideraciones
- Autenticación: el módulo asume usuarios autenticados para la mayoría de rutas (excepto la redirección en index si no hay auth).
- Integración con Users: existe `customers.user_id` (nullable/unique). Si se asocia un usuario a un cliente, la eliminación del usuario borrará al cliente por cascade.
- Fechas: birth_date se expone formateada vía accessor y se oculta en JSON por defecto.
- JSON/API: listCustomers entrega {status: "success", data: [...]}; store/update/destroy responden {status, message}.
