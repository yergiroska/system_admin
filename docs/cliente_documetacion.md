# Módulo Cliente — Documentación completa

Fecha: 2025-08-22

## 1. Propósito y alcance
El módulo Cliente gestiona el ciclo de vida de los clientes dentro del sistema y permite registrar compras de productos asociados a compañías. Cubre:
- CRUD de clientes (crear, listar, ver, editar, eliminar).
- Exposición de listados en formato HTML y JSON (para consumo AJAX).
- Flujo de compra: selección de productos por compañía, cálculo de totales en el cliente y registro de compras.

Este módulo sigue el patrón MVC de Laravel: rutas → controlador → modelo(s) → vistas.

## 2. Arquitectura general
- Rutas: agrupadas bajo el prefijo `customers` con middleware `auth`. Archivo: `routes/web.php`.
- Controlador: `App\Http\Controllers\CustomerController`.
- Modelo: `App\Models\Customer` (relaciones con `Purchase`, `User`).
- Vistas: `resources/views/customers/*.blade.php` y parcial `resources/views/inc/product_compra.blade.php`.

## 3. Casos de uso y flujo
1) Listar clientes
- Ruta: GET `/customers` → `CustomerController@index`.
- Vista: `customers/index.blade.php`.
- Muestra: nombre, apellido, fecha de nacimiento, DNI, y acciones (editar, eliminar, comprar, ver detalle).

2) Crear cliente
- Ruta: GET `/customers/create` → formulario.
- Ruta: POST `/customers/save` → `CustomerController@store`.
- Validación: `first_name` requerido, `last_name` requerido, `birth_date` `date` requerido, `identity_document` único en `customers`.
- Respuesta: JSON de éxito (flujo actual usa AJAX para notificación).

3) Editar cliente
- Ruta: GET `/customers/{id}/edit` → formulario.
- Ruta: PUT `/customers/{id}/update` → `CustomerController@update`.
- Validación: igual a creación, con regla única que ignora el ID actual.
- Respuesta: JSON (usado por AJAX).

4) Eliminar cliente
- Ruta: DELETE `/customers/{id}/delete` → `CustomerController@destroy`.
- Bitácora: registra un `Log` con acción `ELIMINAR` (tabla/objeto `customers`, detalle JSON).
- Respuesta: JSON y borrado lógico (modelo usa SoftDeletes, aunque el `delete()` invocado elimina según configuración del modelo; al estar `SoftDeletes`, se marca `deleted_at`).

5) Ver lista detallada (AJAX)
- Ruta: GET `/customers/view` muestra la tabla vacía.
- Ruta: GET `/customers/list` → `CustomerController@listCustomers` devuelve JSON con todos los clientes.
- Vista: `customers/view_customers.blade.php` rellena la tabla vía jQuery.

6) Flujo de compra
- Selección: GET `/customers/{id}/get-products` → `CustomerController@getProducts`.
- Vista: `customers/products.blade.php` incluye el parcial `inc/product_compra.blade.php`.
- Interfaz: por cada compañía con productos se listan checkboxes de productos, con precio, campo de cantidad y total.
- JS en parcial:
  - Al marcar un producto: habilita campo de cantidad; al desmarcar: deshabilita, pone cantidad y total "0".
  - Al cambiar cantidad: recalcula total = precio * cantidad.
- Envío: POST `/customers/{id}/buy` → `CustomerController@buy`.
- Persistencia: crea registros `Purchase` relacionándolos con el `Customer`. Guarda `company_product_id`, `unit_price`, `quantity`, `total`.

7) Ver detalle del cliente
- Ruta: GET `/customers/{id}/show` → `CustomerController@show`.
- Vista: `customers/show.blade.php`.
- Carga relaciones: `purchases.companyProduct.company` y `purchases.companyProduct.product`.
- Tabla de compras con: compañía, producto, precio, cantidad, total, fecha.

## 4. Componentes en detalle
### 4.1 Rutas (routes/web.php)
- Prefijo: `customers`, nombre de rutas: `customers.*`, middleware: `auth`.
- Rutas principales: `index`, `create`, `edit`, `show`, `store` (POST save), `update` (PUT), `destroy` (DELETE), `get_products`, `buy`, `view`, `lists` (JSON).

### 4.2 Controlador (CustomerController)
Responsabilidades:
- Autenticación: protege rutas a través del grupo con middleware.
- Validación: usa `$request->validate()` con reglas de negocio básicas.
- Persistencia: crea/actualiza/elimina registros `Customer`.
- Logging: registra `CREAR` y `ELIMINAR` en `Log` con `user_id` e IP (placeholder por ahora).
- API JSON: respuestas estandarizadas para AJAX (`status`, `message`, `data`).
- Flujo de compras: recibe arreglo `products`, filtra seleccionados y guarda `Purchase` para el cliente.

Puntos destacables del código:
- Encadenamiento de setters en el modelo para mayor legibilidad: `setFirstName()->setLastName()->...`.
- `show()` usa `with()` para precargar relaciones, mejorando eficiencia.
- `buy()` transforma el arreglo de productos con `collect()->filter()->map()->all()` antes de persistir.

### 4.3 Modelo (Customer)
- Tabla: `customers`.
- SoftDeletes: maneja `deleted_at`.
- Fillable: `first_name`, `last_name`, `birth_date`, `identity_document`, `deleted_at`, `user_id`.
- Casts: `birth_date` como `date:Y-m-d`, `deleted_at` con formato datetime.
- Getters de conveniencia: `getFullName()`, `getFirstName()`, `getLastName()`, `getBirthDate()`, `getBirthDateForm()`, `getIdentityDocument()`.
- Setters: `setFirstName()`, `setLastName()`, `setBirthDate()`, `setIdentityDocument()`, `setUserId()`.
- Relaciones: `purchases()` (HasMany), `user()` (BelongsTo).

Nota: la relación `purchases` implica la existencia del modelo `Purchase` con una `companyProduct` que enlaza `product` y `company`.

### 4.4 Vistas
- `customers/index.blade.php`: lista clientes y ofrece acciones.
- `customers/create.blade.php`: formulario de creación con AJAX.
- `customers/edit.blade.php`: formulario de edición con AJAX.
- `customers/products.blade.php`: marco de compra, incluye parcial.
- `inc/product_compra.blade.php`: UI de compra por compañía y producto, con cálculo de totales en el navegador.
- `customers/show.blade.php`: detalle del cliente con su historial de compras.
- `customers/view_customers.blade.php`: tabla renderizada por AJAX desde `/customers/list`.

## 5. Modelo de datos y nomenclatura
Tablas relevantes:
- `customers` (clientes):
  - Recomendado: id (PK), first_name, last_name, birth_date (date), identity_document (unique), user_id (FK opcional), created_at, updated_at, deleted_at.
- `purchases` (compras):
  - Recomendado: id (PK), customer_id (FK), company_product_id (FK), unit_price (decimal 10,2), quantity (int), total (decimal 10,2), created_at, updated_at.
- `company_products` (catálogo de productos por compañía con precios):
  - Recomendado: id (PK), company_id (FK), product_id (FK), price (decimal 10,2), created_at, updated_at.

Convenciones de nombres sugeridas:
- Tablas: plural en snake_case: `customers`, `purchases`, `company_products`.
- Columnas: snake_case descriptivo: `identity_document`, `unit_price`, `created_at`.
- Claves foráneas: `<singular>_id`.

## 6. Seguridad, validaciones y permisos
- Autenticación: todas las rutas de `customers` usan `auth`.
- Validación en servidor: `store` y `update` validan campos obligatorios y unicidad de `identity_document`.
- CSRF: formularios Blade incluyen `@csrf`; peticiones AJAX adjuntan el token.
- Errores: vistas muestran errores de validación. Se recomienda estandarizar respuestas de error JSON.
- Permisos/roles: actualmente no diferenciados. Recomendación: usar Gates/Policies de Laravel para CRUD y compras.

## 7. Errores comunes y manejo
- Cantidad en `product_compra`: el input comienza en 0 y deshabilitado; solo se habilita al marcar el checkbox. Si se envía con 0, la compra se registrará con 0 (recomendado validar > 0 en servidor).
- Dependencia del precio del cliente: el precio proviene de `companyProduct` (pivote). Si falta el precio, se envía `0.00`. Recomendado validar en servidor que cada ítem seleccionado tenga precio válido.

## 8. Recomendaciones de mejora (SOLID, patrones y limpieza)
1) Validación de compras en servidor (S de SOLID: responsabilidad única)
- En `buy()`, antes de persistir:
  - Validar que `products` exista y sea arreglo.
  - Para cada ítem: `id` requerido, `quantity` integer > 0, `price` decimal >= 0, `total` = `price * quantity` (recalcular en servidor para evitar manipulación).

2) Service Layer (S, O)
- Extraer la lógica de compra a un servicio `PurchaseService`:
  - `registerCustomerPurchases(Customer $customer, array $items): void`.
  - Permite testear y reutilizar, y mantiene el controlador delgado.

3) DTOs / Request Objects (I, D)
- Usar Form Requests (`StoreCustomerRequest`, `UpdateCustomerRequest`, `BuyProductsRequest`) para centralizar reglas y mensajes.
- Un DTO `PurchaseItem` con propiedades tipadas (`companyProductId`, `unitPrice`, `quantity`) y método `total()`.

4) Repositorios (D de SOLID)
- Interface `CustomerRepositoryInterface` y `EloquentCustomerRepository` si se prevé cambiar la fuente de datos.

5) Named Constructors (Patrón) en `Customer`
- Añadir `public static function fromArray(array $data): self` para crear clientes de forma expresiva.
- Ejemplo:
```php
public static function fromArray(array $data): self {
    return (new self())
        ->setFirstName($data['first_name'])
        ->setLastName($data['last_name'])
        ->setBirthDate($data['birth_date'])
        ->setIdentityDocument($data['identity_document']);
}
```

6) Factory (Patrón)
- Si hay reglas más complejas, crear `CustomerFactory` de dominio (no confundir con las factories de testing) para instanciar clientes con invariantes.

7) Open/Closed (O)
- Encapsular formatos de salida (por ejemplo, método `formattedBirthDate()` en el modelo, o un Presenter) para no tocar vistas si cambia el formato.

8) Single Responsibility (S)
- Mover el registro de `Log` a un Observer (`CustomerObserver@created` | `@deleted`) o a eventos `CustomerCreated`, `CustomerDeleted` + Listeners.

9) Validaciones de UI
- En `product_compra.blade.php` agregar validación de cantidad numérica positiva y formateo de totales a 2 decimales.
- Deshabilitar el botón comprar si no hay productos seleccionados o cantidades válidas.

10) Seguridad adicional
- Recalcular `unit_price` desde `company_products` en servidor ignorando el valor enviado por el cliente para evitar manipulación.
- Verificar que `company_product_id` existe y está activo, y que el cliente tiene permiso para comprarlo.

11) Rendimiento
- Paginación en `index()` y `listCustomers()` en lugar de `all()`.
- Seleccionar solo columnas necesarias para listados.

12) Nombres y consistencia
- Usar español o inglés de forma consistente. Actualmente hay mezcla: métodos en inglés en modelo/log, vistas en español. Recomendar uniformar: p. ej., todo en español para vistas/textos y nombres de DB; métodos de dominio en inglés o español, pero consistente.

## 9. Ejemplos de endpoints y respuestas
- Listado JSON de clientes: GET `/customers/list`
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "first_name": "Juan",
      "last_name": "Pérez",
      "birth_date": "1990-05-10",
      "identity_document": "12345678",
      "formatted_birth_date": "10-05-1990"
    }
  ]
}
```

- Registro de compra (request esperado)
```json
{
  "products": {
    "15": {"id": 15, "price": 25.50, "quantity": 2, "total": 51.00},
    "20": {"id": 20, "price": 100.00, "quantity": 1, "total": 100.00}
  }
}
```

## 10. Checklist de calidad sugerido
- [ ] Rutas con middleware adecuado.
- [ ] Validaciones en Form Requests.
- [ ] Servicios para lógica de negocio (compras).
- [ ] Recalcular totales en servidor.
- [ ] Observers/Eventos para logging.
- [ ] Paginación y ordenamiento en listados.
- [ ] Pruebas unitarias y de feature (CRUD y compra).
- [ ] Mensajes de error consistentes (JSON y vistas).

## 11. FAQ
- ¿Por qué el formulario de creación/edición responde en JSON? 
  - Las vistas usan AJAX para enviar y mostrar notificaciones sin recargar la página. Se puede adaptar a redirecciones clásicas si se desea.

- ¿Dónde se guardan las compras? 
  - En la tabla `purchases`, asociadas a `customers` y a un `company_product` (producto ofertado por una compañía con su precio).

- ¿De dónde sale el precio del producto? 
  - Del pivote/entidad `company_products`. En el formulario se incluye como `data-price`; en servidor se recomienda recalcularlo para evitar manipulación.

- ¿Puedo cambiar el formato de fecha mostrado? 
  - Sí. Usar `getBirthDate()` del modelo o un Presenter para centralizar el formato.

- ¿Cómo evitar que se registren compras con cantidad 0? 
  - Validar en el servidor en `buy()` (o en un `BuyProductsRequest`) que `quantity` sea > 0 y que `total` coincida con `unit_price * quantity`.

## 12. Roadmap mínimo de mejoras (no disruptivo)
1. Crear `StoreCustomerRequest`, `UpdateCustomerRequest`, `BuyProductsRequest`.
2. Extraer `PurchaseService` y mover la lógica de `buy()`.
3. Recalcular precios desde `company_products` y no desde el request.
4. Implementar paginación en `index()` y `listCustomers()`.
5. Añadir tests de Feature: crear, actualizar, borrar, listar y comprar.

---
Este documento resume y propone mejoras concretas del módulo Cliente para hacerlo más robusto, mantenible y seguro, manteniendo la coherencia con las prácticas de Laravel y principios SOLID.
