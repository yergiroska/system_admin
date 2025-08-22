# Documentación del Módulo de Compañías (Companies)

Fecha: 2025-08-22 09:44

Este documento describe de forma detallada el módulo de Compañías, su propósito, arquitectura, componentes, flujos de trabajo, validaciones, rutas/endpoints, relaciones con otras entidades y consideraciones de mantenimiento. Además, incluye recomendaciones de mejora basadas en principios SOLID y patrones de diseño (Service, Repository, Factory/DTO, Named Constructors), propuestas de nombres de tablas/campos y un FAQ.


## 1. Propósito y Alcance
- ¿Qué hace?: Permite gestionar Empresas/Compañías en el sistema (crear, listar, ver detalle, editar, eliminar) y asociarlas con Productos mediante una relación muchos-a-muchos con atributos adicionales (p. ej., price en la tabla pivote).
- ¿Para qué sirve?: Centraliza el directorio de empresas y su relación con el catálogo de productos, facilitando la administración desde la interfaz web y operaciones vía AJAX.
- ¿Quién lo usa?: Usuarios autenticados (todas las rutas del módulo están protegidas por middleware auth).


## 2. Arquitectura General
Componentes principales:
- Rutas (routes/web.php): definen endpoints HTTP bajo el prefijo /companies.
- Controlador (app/Http/Controllers/CompanyController.php): orquesta la lógica de aplicación del CRUD, asociaciones con productos, devuelve vistas o JSON y registra logs.
- Modelo (app/Models/Company.php): entidad Eloquent con SoftDeletes y relación belongsToMany con Product (modelo pivote CompanyProduct).
- Vistas Blade (resources/views/companies/*.blade.php): index, create, edit, show, view_companies.
- Parcial de productos (resources/views/inc/products.blade.php): selección de productos y campo de precio por producto cuando se trabaja desde formularios de compañía.
- Logging: Se usa App\Models\Log para registrar operaciones críticas (crear y eliminar).

Flujo a grandes rasgos:
- Listar: GET /companies -> vista con tabla (companies.index). GET /companies/list -> JSON para vista AJAX (companies.view_companies).
- Crear: GET /companies/create -> formulario. POST /companies/save -> crea compañía, asocia productos y registra log. Respuesta JSON (AJAX).
- Ver detalle: GET /companies/{id}/show -> muestra detalle de la compañía.
- Editar: GET /companies/{id}/edit -> formulario. PUT /companies/{id}/update -> actualiza y sincroniza productos. Respuesta JSON.
- Eliminar: DELETE /companies/{id}/delete -> elimina (SoftDelete) y registra log. Respuesta JSON.


## 3. Rutas y Endpoints
Prefijo y nombre: Route::prefix('companies')->name('companies.')->middleware('auth')
- GET /companies (companies.index): lista compañías (vista companies.index).
- GET /companies/create (companies.create): formulario de creación (vista companies.create).
- GET /companies/{id}/edit (companies.edit): formulario de edición (vista companies.edit).
- POST /companies/save (companies.store): crea una nueva compañía. Respuesta JSON.
- PUT /companies/{id}/update (companies.update): actualiza una compañía existente. Respuesta JSON.
- DELETE /companies/{id}/delete (companies.destroy): elimina (SoftDelete) una compañía. Respuesta JSON.
- GET /companies/view (companies.view): vista que consume JSON para listar (companies.view_companies).
- GET /companies/list (companies.lists): devuelve JSON con todas las compañías (id, name, description, url_detail).
- GET /companies/{id}/show (companies.show): muestra el detalle de una compañía.

Ejemplo de respuesta JSON (companies.lists):
{
  "status": "success",
  "data": [
    {"id": 1, "name": "Empresa A", "description": "Desc", "url_detail": "/companies/1/show"},
    {"id": 2, "name": "Empresa B", "description": "Desc", "url_detail": "/companies/2/show"}
  ]
}


## 4. Controlador CompanyController
Ubicación: app/Http/Controllers/CompanyController.php

Acciones principales:
- __construct(): aplica middleware('auth') para proteger todas las acciones.
- index(): obtiene todas las compañías y retorna vista companies.index.
- create(): obtiene todos los productos y retorna vista companies.create (incluye parcial inc/products para asociar productos y precios por compañía).
- store(Request $request):
  - Valida datos de entrada: name requerido y único, description requerida.
  - Crea Company y guarda.
  - Asocia productos vía $company->products()->attach($request->products ?? []).
  - Registra log (acción CREAR) con detalle en JSON y user_id del usuario autenticado.
  - Retorna JSON { status: success, message }.
- viewCompanies(): retorna vista companies.view_companies (listado que se alimenta por AJAX).
- listCompanies(): retorna JSON con todas las compañías, incluyendo url_detail para cada una.
- show($id): carga compañía y retorna vista companies.show.
- edit($id): carga compañía y todos los productos, retorna vista companies.edit (incluye inc/products).
- update($id, Request $request):
  - Valida datos requeridos (name, description).
  - Actualiza Company y guarda.
  - Sincroniza productos vía $company->products()->sync($request->products ?? []).
  - Retorna JSON { status: success, message }.
- destroy($id):
  - Registra log (acción ELIMINAR) con detalle de la compañía.
  - Elimina la compañía (SoftDelete via trait en el modelo).
  - Retorna JSON { status: success, message }.

Notas importantes:
- store/update/destroy devuelven JSON, facilitando el uso de formularios vía AJAX en las vistas create/edit.
- Se utiliza auth()->user()->id para los logs; requiere sesión iniciada.
- Es recomendable envolver creación/actualización/eliminación + asociaciones + logs en transacciones para garantizar consistencia (ver Recomendaciones).


## 5. Modelo Company
Ubicación: app/Models/Company.php
- SoftDeletes habilitado.
- Fillable: name, description, deleted_at.
- Getters/Setters: getId/getName/getDescription y setName/setDescription.
- Relación con Product (many-to-many):
  $this->belongsToMany(Product::class)
      ->using(CompanyProduct::class)
      ->as('companyProduct')
      ->withPivot(['id', 'price'])
      ->withTimestamps();

Observaciones:
- withPivot(['id','price']) asume que la columna price existe en la tabla pivote (company_product). Ver sección 8 para confirmar/añadir.


## 6. Vistas y Parcial de Productos
Vistas principales (resources/views/companies/):
- index.blade.php: tabla con compañías y acciones Editar/Eliminar. Eliminación vía AJAX DELETE a companies.destroy.
- create.blade.php: formulario de creación vía AJAX (POST a companies.store). Incluye @include('inc.products') para seleccionar productos y, opcionalmente, precios específicos para esta compañía.
- edit.blade.php: formulario de edición vía AJAX (PUT a companies.update). Incluye inc/products para mostrar productos ya asociados y sus precios.
- show.blade.php: detalle de una compañía.
- view_companies.blade.php: renderiza tabla a partir de JSON de companies.lists.

Parcial de productos (resources/views/inc/products.blade.php):
- Renderiza lista de productos con checkbox por producto y un input de precio. El input de precio se habilita cuando el checkbox está marcado.
- Naming actual de inputs cuando se usa desde compañías:
  - Checkbox: name="products[{productId}][__checked]" value="{productId}"
  - Precio: name="products[{productId}][price]"
- JS: habilita/deshabilita el input de precio según el estado del checkbox.

Consideración importante sobre el contrato de datos: Eloquent espera, para attach/sync con atributos pivote, un mapa del estilo [productId => ['price' => x]]. Si se envían claves como "__checked", pueden intentar persistirse como columna pivote inexistente. Ver sección 9 para alineación del contrato.


## 7. Validación y Seguridad
- Validación en CompanyController:
  - store(): name requerido y único en companies.name; description requerida.
  - update(): name requerido; description requerida. (No se valida unicidad aquí; considere agregarla con Rule::unique()->ignore($id)).
- Seguridad: todas las rutas del módulo están protegidas por middleware auth.
- Logs: operaciones de crear y eliminar registradas con App\Models\Log, incluyendo objeto afectado, detalle JSON, ip (valor estático en código) y user_id.


## 8. Base de Datos y Relación Pivot
- Tabla principal: companies (id, name, description, deleted_at, timestamps).
- Tabla productos: products (id, name, description, deleted_at, timestamps).
- Tabla pivote: company_product (id, company_id, product_id, timestamps, softDeletes?)
- Atributo adicional esperado: price (decimal) en la tabla pivote.

Inconsistencias potenciales:
- El modelo Company y el parcial inc/products contemplan un campo price en la pivote, pero la migración de la pivote podría no tener esa columna. Si falta la columna, cualquier intento de attach/sync con ['price' => ...] fallará con "Unknown column 'price'".

Opciones de solución:
1) Añadir la columna price a la tabla pivote mediante migración de alteración:
   Schema::table('company_product', function (Blueprint $table) {
       $table->decimal('price', 12, 2)->nullable()->after('product_id');
   });
2) O retirar temporalmente el uso de price del modelo (withPivot) y de las vistas si no se necesita.

Índices/constraints recomendados:
- Unique compuesto (company_id, product_id).
- Índices por company_id y product_id para mejorar performance en joins.


## 9. Contrato de Datos para Asociaciones (attach/sync)
Estado actual observado (formularios de compañía):
- El parcial inc/products envía:
  - products[{productId}][__checked]
  - products[{productId}][price]
- El controlador hace: attach($request->products ?? []) / sync($request->products ?? []).

Riesgo: Eloquent interpretará el array tal cual y podría intentar insertar un atributo pivote __checked, que no existe en la tabla company_product.

Recomendación para alinear (sin cambiar la vista, normalizando en el controlador):
- En store/update, transformar $request->products al formato esperado por Eloquent:
  $toAttach = [];
  foreach (($request->input('products', []) as $productId => $payload)) {
      // Considerar "seleccionado" si existe la clave __checked
      if (array_key_exists('__checked', (array) $payload)) {
          $attrs = [];
          if (isset($payload['price'])) { $attrs['price'] = $payload['price']; }
          $toAttach[(int) $productId] = $attrs;
      }
  }
  // Crear: $company->products()->attach($toAttach);
  // Actualizar: $company->products()->sync($toAttach);

Alternativas:
- Cambiar el parcial para enviar products[{productId}][id] en lugar de __checked, o un arreglo simple de ids + un mapa separado de precios.
- Crear un Form Request específico para Company que valide productos y precios (ver sección 12).


## 10. Manejo de Errores y Respuestas
- Respuestas JSON estándar en store/update/destroy con keys: status, message.
- Recomendado: uniformar estructura de errores: { status: 'error', message, errors: { campo: ["mensaje"] } } y códigos HTTP apropiados (422 para validación, 500 para errores internos).
- Envolver operaciones críticas en DB::transaction para asegurar atomicidad de (compañía + asociaciones + log).


## 11. Recomendaciones de Diseño (SOLID, Patrones y Naming)
- Single Responsibility (S):
  - Extraer lógica de negocio a un servicio (CompanyService) que gestione crear/actualizar, normalizar asociaciones y registrar logs.
- Open/Closed & Liskov:
  - Definir interfaces para repositorios (CompanyRepositoryInterface) y depender de abstracciones.
- Interface Segregation & Dependency Inversion:
  - Controlador depende de interfaces inyectadas por IoC. Facilita pruebas y cambios de persistencia.

Patrones sugeridos:
- Service Layer: CompanyService::create(CompanyDTO $dto), ::update(int $id, CompanyDTO $dto).
- Repository: CompanyRepository con métodos all, find, save, delete.
- DTO/Value Object: CompanyDTO { name, description, products: array<ProductAttach> } y ProductAttach { productId, price? }.
- Factory: CompanyFactory para construir la entidad a partir del DTO.
- Observer/Events: usar eventos (CompanyCreated, CompanyDeleted) u Observers de Eloquent para logging/auditoría.
- Named Constructors en el Modelo:
  class Company extends Model {
      public static function fromDTO(CompanyDTO $dto): self {
          $c = new self();
          $c->name = $dto->name;
          $c->description = $dto->description;
          return $c;
      }
  }

Naming y consistencia:
- Usar snake_case en DB y claves foráneas: company_id, product_id.
- Alinear contrato de datos para products en todo el flujo: evitar claves "__checked" en la capa de persistencia; normalizarlas antes de attach/sync.
- Si price se usará, garantizar su existencia en la pivote y en withPivot.


## 12. Validaciones Sugeridas (Form Request)
Crear un Form Request p. ej. CompanyRequest para encapsular reglas:
- name: required|string|max:255|unique:companies,name (en update: Rule::unique('companies','name')->ignore($company->id))
- description: required|string
- products: sometimes|array
- products.*: array
- products.*.price: nullable|numeric|min:0

Si se cambia el contrato para usar products.*.product_id:
- products.*.product_id: sometimes|integer|exists:products,id

En el controlador, normalizar a mapa [productId => ['price' => x]] antes de attach/sync (ver sección 9).


## 13. Performance y UX
- Eager Loading: cuando se muestren compañías con sus productos, usar with('products') para evitar N+1 queries.
- Respuestas AJAX: estandarizar estructura { status, message, data, errors }.
- UX: además del alert, mostrar validaciones específicas por campo en formularios (renderizar errores junto a cada input).


## 14. Pruebas (Testing)
Sugerencias de tests Feature:
- Crear compañía con productos + precio (si existe la columna price en la pivote), validar respuesta JSON y persistencia.
- Actualizar compañía, sincronizando productos (agregar, quitar, actualizar precio).
- Eliminar compañía (SoftDelete) y verificar registro de log.
- Validación: name y description requeridos; unicidad de name en create; estructura de products.

Unidad:
- Probar CompanyService/normalización de payload (si se implementa) con casos edge (sin productos, con precio nulo, con claves no válidas).


## 15. Mejoras de Código Recomendadas (no disruptivas)
- Normalización de products en store/update (evitar enviar __checked a la pivote) antes de attach/sync.
- Envolver store/update/destroy en DB::transaction y try/catch con respuesta JSON de error consistente.
- Añadir Rule::unique()->ignore($id) en update para name si se desea garantizar unicidad.
- Extraer el valor de IP de la request real ($request->ip()) en logs en lugar del literal '4444'.

Ejemplo de normalización (conceptual):
$payload = $request->input('products', []);
$toAttach = [];
foreach ($payload as $productId => $row) {
    if (array_key_exists('__checked', (array) $row)) {
        $attrs = [];
        if (isset($row['price'])) { $attrs['price'] = $row['price']; }
        $toAttach[(int) $productId] = $attrs;
    }
}
$company->products()->attach($toAttach); // o sync($toAttach) en update


## 16. Propuestas de nombres de tablas y campos
- companies: id, name, description, deleted_at, timestamps.
- products: id, name, description, deleted_at, timestamps.
- company_product (pivote): id, company_id, product_id, price?, timestamps, deleted_at?.
- Índices: unique(company_id, product_id), índices por company_id y product_id.

Si la gestión de precios por compañía y producto escala (múltiples monedas, vigencias), considerar mover price a una tabla histórica (company_product_prices) con (id, company_id, product_id, price, currency_code, valid_from, valid_to, created_at).


## 17. Ejemplos prácticos
- Payload recomendado (tras normalización) para asociar productos en store/update:
  products = {
    10: { "price": 15.90 },
    12: { "price": null }
  }
- Respuesta JSON típica de éxito:
  { "status": "success", "message": "Empresa creada con exito." }
- Respuesta JSON de error sugerida:
  { "status": "error", "message": "No se pudo crear la empresa.", "errors": { "name": ["El nombre es requerido."] } }


## 18. Checklist de integración y despliegue
- Confirmar que la tabla company_product existe y, si se usará price, que la columna esté creada.
- Ejecutar migraciones/seeders pendientes.
- Verificar que el contrato del formulario (inc/products) esté alineado con la normalización previa al attach/sync.
- Probar flujos CRUD vía UI y AJAX (create/edit/delete y listados JSON).
- Revisar logs de operaciones (crear/eliminar) y que el user_id e IP sean correctos.


## 19. Preguntas Frecuentes (FAQ)
1) ¿Cómo asocio productos y precios a una compañía?
- En los formularios de Crear/Editar, marca los productos deseados. El parcial inc/products envía products[{productId}][__checked] y products[{productId}][price]. Se recomienda normalizar a un mapa [productId => { price }] antes de attach/sync.

2) Recibo "Unknown column 'price' in 'field list'" al guardar.
- La tabla company_product probablemente no tiene la columna price. Añádela con una migración (ver sección 8) o retira temporalmente el uso de price.

3) ¿Qué formato de datos espera el endpoint de creación/actualización?
- Mínimo: { name: string, description: string }. Opcional: products como mapa de productId a atributos pivote (p. ej., { 10: { price: 15.90 } }).

4) ¿Puedo eliminar una compañía asociada a productos?
- Sí. Se aplica SoftDelete sobre companies. La pivote se mantiene. Puedes restaurar con Company::withTrashed()->find($id)->restore().

5) ¿Cómo evitar N+1 al listar compañías con sus productos?
- Usa eager loading: Company::with('products')->get().

6) ¿Cómo valido que el precio sea obligatorio si se selecciona un producto?
- Backend: regla condicional (p. ej., validación personalizada) para exigir price cuando exista __checked. Frontend: el input ya se habilita solo si la casilla está marcada.

7) ¿Cómo registro logs sin acoplar el controlador?
- Implementa eventos (CompanyCreated, CompanyDeleted) u Observers de Eloquent para mover el registro de logs fuera del controlador.


## 20. Consideraciones finales
El módulo de Compañías es funcional para el CRUD y soporta operaciones vía AJAX. Para robustez y mantenibilidad, es clave alinear el contrato de datos de products con el attach/sync de Eloquent, verificar la existencia de la columna price en la pivote si se usará, y considerar una capa de servicio con validaciones y transacciones. Las recomendaciones incluidas permiten evolucionar el módulo conforme a buenas prácticas (SOLID) y patrones de diseño habituales en Laravel.
