# Propuesta de mejora y análisis del proyecto

Este documento ofrece un análisis técnico del proyecto (módulos, rutas, migraciones, modelos, controladores y vistas) y una propuesta de mejoras con acciones concretas. La intención es servir como hoja de ruta priorizada.

Fecha: 2025-08-15

---

## 1. Resumen del proyecto

Aplicación Laravel orientada a la gestión de:
- Autenticación de usuarios y registro
- Empresas (Companies)
- Productos (Products) y su asociación con empresas mediante un pivot (company_product) con precio
- Clientes (Customers) y sus compras (Purchases)
- Notas (Notes)
- Logs de actividad (Logs) y registros de sesiones (UserLogins)

Arquitectura MVC estándar de Laravel, con vistas Blade y controladores RESTful para CRUDs. Se usan relaciones Many-to-Many con pivot personalizado (CompanyProduct) y SoftDeletes en varios modelos.

---

## 2. Módulos principales detectados

1) Autenticación
- Controlador: App\Http\Controllers\AuthController
- Vistas: resources/views/auth/login.blade.php, auth/register.blade.php
- Funcionalidades: registro, login, logout, creación automática de Customer al registrarse, registro de inicios de sesión (UserLogin).

2) Dashboard
- Controlador: App\Http\Controllers\HomeController (dashboard)
- Vista: resources/views/dashboard.blade.php
- Incluye formulario de compra vía partial inc/product_compra (no mostrado en historial, pero referenciado).

3) Empresas (Companies)
- Controlador: CompanyController (CRUD + listados JSON y show)
- Modelo: Company (SoftDeletes, relación belongsToMany con Product usando CompanyProduct como pivot y alias companyProduct)
- Vistas: companies.index/create/edit/show (show existe; index/create/edit no se listaron pero se asumen)

4) Productos (Products)
- Controlador: ProductController (CRUD + listados JSON)
- Modelo: Product (SoftDeletes, belongsToMany con Company con pivot CompanyProduct)
- Vistas: products.index/create/edit/view_products (create existe; index/edit/view_products referenciadas en rutas; inc.companies parcial para selección de empresas y precio)

5) Clientes (Customers)
- Controlador: CustomerController (CRUD, listar, ver, compras, ver productos por cliente)
- Modelo: Customer (SoftDeletes, hasMany Purchases, belongsTo User)
- Vistas: customers.index/create/edit/view_customers/show/products

6) Compras (Purchases)
- Modelo: Purchase (belongsTo Customer, belongsTo CompanyProduct)
- Persistencia: CustomerController@buy guarda compras enlazando company_product_id

7) Notas (Notes)
- Controlador: NoteController (no se listó completo, pero rutas CRUD existen)
- Vistas: notes.* (al menos show existe)

8) Logs (Logs)
- Controlador: LogController (index, details)
- Modelo: Log (no listado, pero usado en varios controladores)
- Vistas: logs.index, logs.details

9) Registros de inicio de sesión (UserLogins)
- Controlador: UserLoginController@details
- Modelos: User, UserLogin
- Vistas: users_logins.details

---

## 3. Rutas (routes/web.php)

- Autenticación
  - GET / (login form) name: login
  - POST /auth name: post.login
  - GET /register name: register
  - POST /register name: post.register
  - POST /logout name: logout

- Dashboard
  - GET /dashboard (auth) name: dashboard

- Customers (auth, prefix customers, name customers.)
  - GET /, /create, /{id}/edit, /{id}/show
  - GET /{id}/get-products name: get_products
  - POST /save name: store, PUT /{id}/update name: update, DELETE /{id}/delete name: destroy
  - POST /{id}/buy name: buy
  - GET /view name: view, GET /list name: lists

- Products (auth, prefix products, name products.)
  - GET /, /create, /{id}/edit
  - POST /save name: store, PUT /{id}/update name: update, DELETE /{id}/delete name: destroy
  - GET /view name: view, GET /list name: lists

- Notes (auth, prefix notes, name notes.)
  - GET /, /create, /{id}/edit
  - POST /save, PUT /{id}/update, DELETE /{id}/delete
  - GET /view, GET /list, GET /{id}/show

- Companies (auth, prefix companies, name companies.)
  - GET /, /create, /{id}/edit, /{id}/show
  - POST /save name: store, PUT /{id}/update name: update, DELETE /{id}/delete name: destroy
  - GET /view name: view, GET /list name: lists

- Logs (auth, prefix logs, name logs.)
  - GET /, GET /{id}/details

- Users (auth, prefix users, name users.)
  - GET /

- UsersLogins (auth, prefix users-logins, name user_login.)
  - GET /{id}/details

---

## 4. Migraciones relevantes

- 2025_08_04_072820_create_company_products_table.php
  - Crea tabla company_product (id autoincremental, company_id, product_id, unique pair, timestamps, softDeletes)
  - Observación: El modelo CompanyProduct usa price, pero esta migración no define la columna price. Posible falta de migración para añadir price.

- (No se listaron las demás migraciones, pero existen tablas: users, customers, companies, products, notes, logs, user_logins, purchases.)

---

## 5. Modelos y relaciones

- Company (SoftDeletes)
  - belongsToMany(Product) -> using CompanyProduct (Pivot personalizado)
  - alias companyProduct, withPivot(['id','price']), withTimestamps()

- Product (SoftDeletes)
  - belongsToMany(Company) -> using CompanyProduct
  - alias companyProduct, withPivot(['id','price']), withTimestamps()

- CompanyProduct (Pivot)
  - Tabla: company_product, PK id, timestamps true, fillable incluye price
  - belongsTo Company, belongsTo Product

- Customer (SoftDeletes)
  - hasMany Purchases
  - belongsTo User (inversa 1–1)

- Purchase
  - belongsTo Customer
  - belongsTo CompanyProduct (company_product_id)

- User, UserLogin, Log, Note (no se listaron completamente, pero se usan)

---

## 6. Controladores y puntos clave

- AuthController
  - Registro valida y crea User, Customer y UserLogin; marca usuario conectado y establece last_session
  - Login actualiza is_connected y last_session; genera UserLogin
  - Logout actualiza end_connection en último UserLogin

- CompanyController, ProductController
  - CRUDs con logs en create/delete; sincronización/attach de relaciones
  - ProductController y CompanyController asumen que el precio se gestiona vía formulario inc.companies (precio por empresa)

- CustomerController
  - CRUD + list JSON
  - buy(): guarda compras por company_product_id en Purchase
  - show(): carga compras con relaciones anidadas para mostrar compañía, producto y precio

- LogController
  - Listado y detalle de logs (detail guardado como JSON)

---

## 7. Vistas destacadas

- auth/login, auth/register (plantilla layouts.login)
- dashboard (layouts.app) incluye inc.product_compra
- customers/products: incluye inc.product_compra
- inc/companies: checkbox de compañías y campo de precio por compañía
  - Habilita/inhabilita input de precio en función del checkbox
  - Usa $product->companies->firstWhere('id', $company->id)?->companyProduct?->price para precargar precio

---

## 8. Hallazgos y problemas detectados

1) Falta columna price en tabla company_product
- El modelo y las vistas usan price, pero la migración listada no la crea. Esto causará errores al guardar/previsualizar precios.
- Acción: Añadir migración para agregar columna decimal('price', 10, 2)->nullable() o actualizar migración si es permisible recrear.

2) Falta del parcial inc/product_compra en el historial
- Referenciado en dashboard y customers/products. Asegurar su existencia y correcta implementación.

3) Falta de políticas/permiso por roles
- Actualmente cualquier usuario autenticado accede a todos los módulos.

4) Validaciones y UX
- Vistas usan AJAX en products/create, pero no manejan errores detallados.

5) Tests automatizados y seeders
- No se evidencian pruebas específicas para flujos críticos ni seeders de datos base.

6) Logs con IP fija
- Se usan valores estáticos ("1111", "2222", "4444"). Debería usarse $request->ip().

7) Integridad en compras
- buy() asume que products (IDs) vienen del formulario; falta validar existencia de company_product y que pertenece a la combinación escogida.

---

## 9. Qué le hace falta al proyecto (recomendaciones)

A. Roles y permisos (RBAC)
- Implementar spatie/laravel-permission o políticas nativas
- Roles sugeridos: Admin, Gestor, Vendedor, Cliente
  - Admin: total control
  - Gestor: CRUD Companies/Products/Notes, acceso a Logs
  - Vendedor: gestión de clientes y compras
  - Cliente: ver su propio dashboard y compras

B. Historial de precios por empresa y producto
- Crear tabla company_product_prices: id, company_product_id, price, valid_from, valid_to (nullable), created_at
- Mantener en company_product el precio vigente (opcional) o calcular “precio actual” como último registro sin valid_to
- Permitir consultar precios actuales y anteriores por cliente/compañía/producto

C. Mejoras de UX en formularios
- Manejo de errores AJAX con feedback amigable
- Confirmaciones de guardado/actualización y redirecciones adecuadas
- Campos deshabilitados/activados coherentemente en inc/companies

D. Datos maestros y seeders
- Seeders para Roles/Permisos, Empresas demo, Productos demo, Usuarios de prueba

E. Auditoría y seguridad
- Capturar IP real: $request->ip()
- Registrar user-agent
- Políticas para operations sensibles

F. API REST pública/privada (opcional)
- Endpoints para listar Companies, Products, Prices, Customers y Purchases
- Autenticación Sanctum

G. Pruebas automatizadas
- Feature tests: autenticación, CRUDs, compra
- Unit tests: lógica de precio vigente, relaciones

---

## 10. Ejemplos de campos a añadir

- Tabla company_product (si se prefiere mantener precio vigente en pivot):
  - price decimal(10,2) nullable

- Tabla company_product_prices (nueva):
  - id (PK)
  - company_product_id (FK a company_product)
  - price decimal(10,2)
  - valid_from datetime (por defecto now)
  - valid_to datetime nullable (vigente si null)
  - created_at/updated_at

- Tabla customers (datos personales):
  - first_name, last_name, birth_date, identity_document (ya existen)
  - phone, address (opcional)

- Tabla users (roles/estado):
  - role (si no se usa paquete de permisos), is_connected ya existe

---

## 11. Ejemplos de roles

- Admin: gestionar todo (usuarios, roles, logs, empresas, productos, notas, clientes, compras)
- Gestor: empresas, productos, notas, ver clientes
- Vendedor: gestionar clientes, registrar compras
- Cliente: ver sus compras y perfil

---

## 12. Precios actuales y anteriores (según análisis)

Actualmente:
- Las vistas y modelos asumen un campo price en company_product.
- No existe migración vista que cree price, por lo que no hay precios persistidos correctamente.

Propuesta operativa:
- Añadir columna price en company_product para el “precio actual”.
- Implementar historial en company_product_prices para trazabilidad.

Ejemplo:
- company_product (id=10, company_id=1, product_id=5, price=15.00)
- company_product_prices:
  - (id=1, company_product_id=10, price=12.50, valid_from=2024-01-01, valid_to=2025-01-31)
  - (id=2, company_product_id=10, price=15.00, valid_from=2025-02-01, valid_to=null) -> precio actual

---

## 13. Roadmap de implementación (priorizado)

1) Corregir esquema de precios ✓ (pendiente de ejecutar)
- Migración para añadir price en company_product
- Ajustar formularios para persistir price por empresa al crear/editar productos (ProductController@store/update)
- Validaciones del lado del servidor (precio >= 0)

2) Historial de precios
- Nueva tabla company_product_prices
- Servicios para cambiar precio y cerrar el periodo anterior
- Vistas para mostrar historial

3) Roles/Permisos
- Instalar spatie/laravel-permission
- Semillas de roles, asignación de permisos y middleware

4) Auditoría
- Cambiar IP estática por $request->ip() y almacenar user-agent

5) Pruebas y seeders
- Tests de autenticación, CRUDs, compra y precios
- Seeders de datos base

6) UX y API
- Manejo de errores AJAX y redirecciones
- Endpoints API con Sanctum (opcional)

---

## 14. Acciones inmediatas sugeridas

- Crear migración: add_price_to_company_product_table
- Actualizar ProductController@store y @update para leer companies[<id>][price] y adjuntar/sincronizar con precio (usando ->attach([... => ['price' => x]]) y ->sync([...]))
- Validar en el servidor que si una company está seleccionada, el campo price venga presente y sea numérico >= 0
- Añadir tests básicos para verificar que el precio se guarda y se muestra en customers.show

---

## 15. Ubicación de este documento

- Ruta: docs/propuesta.md

Si necesitas una versión en PDF o un desglose por entregables y estimaciones, puedo generarlo a partir de este documento.

