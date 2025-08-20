# Documentación del Módulo de Productos

Fecha: 2025-08-20 16:11

Este documento describe de forma detallada el módulo de Productos, su propósito, arquitectura, componentes, flujos de trabajo, validaciones, rutas/endpoints, relaciones con otras entidades y consideraciones de mantenimiento. Además, incluye recomendaciones de mejora basadas en principios SOLID y patrones de diseño (Service, Repository, Factory/DTO), junto con ajustes de naming y contrato de datos.


## 1. Propósito y Alcance
- ¿Qué hace?: Permite gestionar Productos en el sistema (crear, listar, editar, eliminar) y asociarlos a Empresas (Company) mediante una relación muchos-a-muchos.
- ¿Para qué sirve?: Centraliza el catálogo de productos y su disponibilidad por empresa, facilitando la administración desde interfaz web y algunas operaciones vía AJAX.
- ¿Quién lo usa?: Usuarios autenticados (todas las rutas del módulo están protegidas por middleware auth).


## 2. Arquitectura General
Componentes principales:
- Rutas (routes/web.php): definen endpoints HTTP bajo el prefijo /products.
- Controlador (app/Http/Controllers/ProductController.php): orquesta la lógica de aplicación del CRUD y devuelve vistas o JSON.
- Modelo (app/Models/Product.php): entidad Eloquent con SoftDeletes y relación belongsToMany con Company (modelo pivot CompanyProduct).
- Form Request (app/Http/Requests/ProductRequest.php): encapsula validaciones para crear/actualizar.
- Vistas Blade (resources/views/products/*.blade.php): index, create, edit, view_products.
- Parcial de empresas (resources/views/inc/companies.blade.php): checkbox de selección y campo de precio por empresa.
- Migración pivot (database/migrations/2025_08_04_072820_create_company_products_table.php): crea la tabla intermedia company_product.
- Logging: El controlador utiliza App\Models\Log para registrar operaciones críticas (crear y eliminar).

Flujo a grandes rasgos:
- Listar: GET /products -> vista con tabla. GET /products/list -> JSON para vistas AJAX (p. ej. view_products).
- Crear: GET /products/create -> formulario. POST /products/save -> crea producto, asocia empresas y registra log. Respuesta JSON (AJAX).
- Editar: GET /products/{id}/edit -> formulario. PUT /products/{id}/update -> actualiza y sincroniza empresas. Respuesta JSON.
- Eliminar: DELETE /products/{id}/delete -> elimina (SoftDelete) y registra log. Respuesta JSON.


## 3. Rutas y Endpoints
Prefijo y nombre: Route::prefix('products')->name('products.')->middleware('auth')
- GET /products (products.index): lista productos en tabla HTML (vista products.index).
- GET /products/create (products.create): muestra formulario de creación (vista products.create).
- GET /products/{id}/edit (products.edit): formulario de edición (vista products.edit).
- POST /products/save (products.store): crea nuevo producto. Respuesta JSON.
- PUT /products/{id}/update (products.update): actualiza un producto. Respuesta JSON.
- DELETE /products/{id}/delete (products.destroy): elimina (SoftDelete) un producto. Respuesta JSON.
- GET /products/view (products.view): vista que consume JSON para listar (products.view_products).
- GET /products/list (products.lists): devuelve JSON con todos los productos.

Ejemplo de respuesta JSON (products.lists):
{
  "status": "success",
  "data": [
    {"id": 1, "name": "Producto A", "description": "Desc"},
    {"id": 2, "name": "Producto B", "description": "Desc"}
  ]
}


## 4. Controlador ProductController
Ubicación: app/Http/Controllers/ProductController.php

Acciones principales:
- index(): obtiene todos los productos y retorna vista products.index.
- create(): obtiene todas las compañías y retorna vista products.create.
- store(ProductRequest $request):
  - Valida datos (mediante ProductRequest).
  - Crea Product y guarda.
  - Asocia compañías via $product->companies()->attach($data['companies'] ?? []).
  - Registra log (acción CREAR).
  - Retorna JSON con status success y mensaje.
- viewProducts(): retorna vista products.view_products para listar vía AJAX.
- listProducts(): retorna JSON con todos los productos.
- edit($id): carga producto y compañías, retorna vista products.edit.
- update($id, ProductRequest $request):
  - Valida datos.
  - Actualiza Product y guarda.
  - Sincroniza compañías via $product->companies()->sync($data['companies'] ?? []).
  - Retorna JSON con status success y mensaje.
- destroy($id):
  - Registra log (acción ELIMINAR) con detalles del producto.
  - Elimina el producto (SoftDelete).
  - Retorna JSON con status success y mensaje.

Notas importantes:
- Respuestas JSON en store/update/destroy permiten trabajar cómodamente con formularios AJAX.
- Se usa auth()->user()->id en logs; requiere sesión iniciada.
- Se recomienda envolver creación/actualización de producto + asociaciones + log en una transacción para consistencia (ver Recomendaciones).


## 5. Modelo Product
Ubicación: app/Models/Product.php
- SoftDeletes habilitado.
- Fillable: name, description, deleted_at.
- Métodos de acceso y mutadores simples getId/getName/getDescription y setName/setDescription.
- Relación con Company (many-to-many):
  $this->belongsToMany(Company::class)
      ->using(CompanyProduct::class)
      ->as('companyProduct')
      ->withPivot(['id', 'price'])
      ->withTimestamps();

Observación crítica: withPivot(['id','price']) asume columna price en la tabla pivot, pero la migración actual no la crea (ver sección 8).


## 6. Validación (ProductRequest)
Ubicación: app/Http/Requests/ProductRequest.php
Reglas:
- name: required|string|max:255
- description: required|string
- companies: sometimes|array
- companies.*: array
- companies.*.id: sometimes|integer|exists:companies,id
- companies.*.price: nullable|numeric|min:0

Observación: el parcial companies.blade.php envía companies[{companyId}][company_id] (no ...[id]). Esto crea un desalineamiento de naming con ProductRequest. Ver sección 9 para alinear el contrato de datos.


## 7. Vistas y Parcial
Vistas principales:
- products/index.blade.php: tabla de productos con acciones Editar/Eliminar. Eliminar via AJAX DELETE a products.destroy.
- products/create.blade.php: formulario de creación vía AJAX (POST a products.store). Mejora aplicada: el textarea ahora usa contenido entre etiquetas en lugar de atributo value.
- products/edit.blade.php: formulario de edición vía AJAX (PUT a products.update). Incluye el parcial de empresas.
- products/view_products.blade.php: lista consumiendo JSON desde products.lists. Mejora aplicada: inicialización de la variable $tr para evitar concatenaciones sobre undefined.

Parcial de empresas:
- resources/views/inc/companies.blade.php: renderiza lista de compañías con checkbox y un input de precio por compañía.
- Naming actual de inputs:
  - Checkbox: name="companies[{companyId}][company_id]" value="{companyId}"
  - Precio: name="companies[{companyId}][price]"
- JS: habilita/deshabilita el input de precio según el estado del checkbox.


## 8. Base de Datos y Relación Pivot
Migración: database/migrations/2025_08_04_072820_create_company_products_table.php
- Tabla: company_product
- Columnas: id, company_id, product_id, timestamps, softDeletes
- Unique: (company_id, product_id)

Inconsistencia detectada:
- El modelo y las vistas contemplan un campo de precio (price) en la tabla pivot, pero la migración no crea esa columna.
- Consecuencia: intentar adjuntar/sincronizar con atributo 'price' dará error SQL (columna desconocida) si se envía un precio.

Opciones de solución (recomendadas):
1) Añadir la columna price al pivot.
   - Nueva migración de alteración:
     Schema::table('company_product', function (Blueprint $table) {
       $table->decimal('price', 12, 2)->nullable()->after('product_id');
     });
2) O bien, retirar el uso de price del modelo (withPivot) y de las vistas/validación si no es necesario.


## 9. Contrato de Datos para Asociaciones (attach/sync)
Estado actual:
- ProductRequest valida companies.*.id, companies.*.price.
- El formulario envía companies[{companyId}][company_id] y companies[{companyId}][price].
- Controlador utiliza attach($data['companies']) y sync($data['companies']).

Recomendación para alinear:
- Opción A (recomendada): Cambiar ProductRequest para aceptar companies.*.company_id (en lugar de .id) y normalizar en el controlador antes de attach/sync en el formato que Eloquent espera:
  $toAttach = [];
  foreach (($data['companies'] ?? []) as $key => $row) {
      if (!empty($row['company_id'])) {
          $companyId = (int) $row['company_id'];
          $attrs = [];
          if (isset($row['price'])) { $attrs['price'] = $row['price']; }
          $toAttach[$companyId] = $attrs;
      }
  }
  $product->companies()->attach($toAttach); // en store
  $product->companies()->sync($toAttach);   // en update

- Opción B: Cambiar el parcial companies.blade.php para enviar companies[*][id] en lugar de companies[*][company_id].

Cualquiera de las dos debe complementarse con la migración del punto 8 si se usará price.


## 10. Manejo de Errores, Seguridad y Logs
- Seguridad: todas las rutas están bajo middleware auth.
- Logs: se registra CREAR y ELIMINAR con App\Models\Log, incluyendo detalle JSON del producto y user_id.
- Recomendaciones:
  - Aplicar políticas (Policies) para autorización granular por acción.
  - Envolver store/update/destroy en DB::transaction(), y capturar excepciones para responder con JSON de error consistente.
  - Validar coherencia de companies para evitar enviar claves vacías.
  - Sanitización de HTML si description pudiese contener marcado.


## 11. Recomendaciones de Diseño (SOLID, Patrones y Naming)
- Single Responsibility (S de SOLID):
  - Extraer lógica de negocio a un servicio (ProductService) que se encargue de crear/actualizar y gestionar asociaciones + logs.
- Open/Closed & Liskov:
  - Diseñar interfaces para repositorios (ProductRepositoryInterface) que permitan alternar persistencia sin cambiar el controlador.
- Interface Segregation & Dependency Inversion:
  - Controlador depende de abstracciones (interfaces) y recibe implementaciones vía IoC (Service Provider).

Patrones sugeridos:
- Service Layer: ProductService::create(ProductDTO $dto), ::update(int $id, ProductDTO $dto).
- Repository: ProductRepository con métodos find, all, create, update, delete.
- DTO/Value Object: ProductDTO { name, description, companies: array<CompanyAttach> } y CompanyAttach { companyId, price? }.
- Factory: ProductFactory para construir la entidad a partir del DTO.
- Observer/Events: usar eventos (ProductCreated, ProductDeleted) u Observers de Eloquent para logging.

Naming y consistencia:
- Unificar el contrato de datos para companies: usar siempre company_id o id, pero no ambos.
- Alinear ProductRequest con el naming de la vista.
- Si se usará price, garantizar que exista la columna y que el attach reciba [companyId => ['price' => x]].


## 12. Performance y UX
- Eager Loading: cuando se listen productos con datos de compañías, usar with('companies') para evitar N+1.
- Respuestas AJAX: estandarizar estructura {status, message, data, errors}.
- UX: mostrar validaciones de backend en formularios (actualmente se muestra un alert; podría renderizarse feedback contextual por campo).


## 13. Pruebas
Sugerencias de tests:
- Feature: crear producto con compañías + precio (si existe columna), actualizar, eliminar; validar respuestas JSON y persistencia.
- Validación: asegurar que name/description son requeridos; que companies.*.company_id existe.
- Unidad: probar ProductService/Repository (si se implementan) con fakes.


## 14. Mejoras aplicadas en esta entrega
- products/create.blade.php: se corrigió el textarea para usar contenido entre etiquetas con old('description').
- products/view_products.blade.php: se inicializó la variable $tr para evitar concatenaciones sobre undefined.

Ambas mejoras son no disruptivas y mejoran la calidad del frontend.


## 15. Roadmap sugerido (prioridad)
1) Corregir inconsistencia de la columna price en pivot (añadir columna o retirar su uso).
2) Alinear contrato de datos companies entre vista, ProductRequest y controlador.
3) Envolver store/update/destroy en transacciones y mejorar manejo de errores JSON.
4) Introducir capa de servicio y repositorio (refactor incremental conforme a SOLID).
5) Añadir pruebas Feature para CRUD y asociación con compañías.
6) Mejorar UX de validaciones en formularios AJAX.


## 16. Ejemplos prácticos
- Payload recomendado para attach/sync (opción A):
  companies = {
    5: {"price": 12.50},
    9: {"price": null}
  }
- Reglas ProductRequest si usamos company_id:
  'companies' => 'sometimes|array',
  'companies.*' => 'array',
  'companies.*.company_id' => 'sometimes|integer|exists:companies,id',
  'companies.*.price' => 'nullable|numeric|min:0'

- Migración para columna price:
  Schema::table('company_product', function (Blueprint $table) {
      $table->decimal('price', 12, 2)->nullable()->after('product_id');
  });


## 17. Consideraciones finales
El módulo de Productos es funcional para el CRUD y está preparado para operar con AJAX, pero requiere alinear el contrato de datos y la estructura de la tabla pivot si se desea soportar precios por empresa. Las recomendaciones incluidas permiten evolucionar el módulo con buen diseño, mantenibilidad y robustez.


## 18. Preguntas Frecuentes (FAQ)
1) ¿Cómo asocio empresas y precios a un producto?
- En el formulario de Crear/Editar, marca la casilla de la empresa y coloca el precio en el campo habilitado. El payload se envía como companies[{companyId}][company_id] y companies[{companyId}][price]. Asegúrate de que la tabla pivot tenga la columna price si vas a persistir este dato.

2) Me aparece el error "Unknown column 'price' in 'field list'" al guardar.
- Causa: la tabla company_product no tiene la columna price. Solución: crear una migración para añadirla (ver secciones 8 y 16) o retirar el uso de price de modelos/vistas/validación.

3) ¿Qué formato de datos espera el endpoint de creación/actualización?
- Mínimo: { name: string, description: string }. Opcional: companies como arreglo asociativo por companyId con atributos pivot, por ejemplo: { 5: { price: 12.50 } } si optas por normalizar antes del attach/sync.

4) ¿Puedo eliminar un producto asociado a empresas?
- Sí. Se aplica SoftDelete sobre products. Las filas de la pivot se mantienen, pero el producto queda oculto por defecto. Puedes restaurarlo con Product::withTrashed()->find($id)->restore().

5) ¿Cómo evito N+1 queries al listar productos con sus empresas?
- Usa eager loading: Product::with('companies')->get(). Documenta/limita las columnas necesarias para performance.

6) ¿Cómo valido que el precio sea obligatorio cuando se selecciona una empresa?
- Backend: 'companies.*.price' => 'required_if:companies.*.company_id,!=,null|numeric|min:0' (puede requerir una validación personalizada). Frontend: habilita el input de precio solo cuando el checkbox esté activo (ya implementado).

7) ¿Cómo puedo adaptar el contrato si necesito enviar companies como lista simple [{id, price}]?
- Cambia el parcial para usar name="companies[index][id]" y ajusta ProductRequest para validar companies.*.id. Alternativamente, transforma en el controlador a un mapa [companyId => ['price' => x]].

8) ¿Cómo registro logs sin acoplar el controlador?
- Usa eventos (ProductCreated, ProductDeleted) u Observers de Eloquent para disparar el registro. Ver sección 11 (Patrones sugeridos).


## 19. Propuestas de nombres de tablas y campos (detallado)
- Tabla principal: products (id, name, description, deleted_at, timestamps)
- Tabla empresas: companies (id, name, description, deleted_at, timestamps)
- Tabla pivot actual: company_product (id, company_id, product_id, price?, timestamps, deleted_at?)
  - Si price es parte del dominio, mantenerla en pivot. Si la gestión de precios escala (monedas, vigencias), conviene separar a company_product_prices con (id, company_id, product_id, price, currency_code, valid_from, valid_to, created_at). En ese caso, la pivot básica mantendría solo las llaves y la tabla de precios modelaría atributos evolutivos.
- Índices/constraints recomendados:
  - Unique compuesto (company_id, product_id) en company_product.
  - Índices por company_id y product_id para acelerar joins.
  - Constraints de integridad referencial ON DELETE CASCADE si decides que al eliminar definitivamente un producto (forceDelete) se limpien pivots.

Convenciones de nombres:
- Usa snake_case en columnas y singular en claves foráneas: company_id, product_id.
- Alinea el contrato del request con el naming del parcial (usar company_id o id, pero uno solo en todo el flujo).


## 20. Ejemplos de patrones (Factory, Named Constructor y DTO)
Nota: son ejemplos de arquitectura propuesta; no están implementados aún en el código actual.

- DTOs mínimos:
  class ProductDTO {
      public function __construct(
          public string $name,
          public string $description,
          /** @var array<int, array{price?: float|null}> */
          public array $companies = []
      ) {}
  }

- Named Constructor en el Modelo (si decides usar Value Objects/DTO y setters centralizados):
  class Product extends Model {
      public static function fromDTO(ProductDTO $dto): self {
          $p = new self();
          $p->name = $dto->name;
          $p->description = $dto->description;
          return $p;
      }
  }

- Factory + Service:
  class ProductFactory {
      public static function make(ProductDTO $dto): Product {
          return Product::fromDTO($dto);
      }
  }

  class ProductService {
      public function __construct(private ProductRepository $repo) {}
      public function create(ProductDTO $dto): Product {
          return \DB::transaction(function () use ($dto) {
              $product = ProductFactory::make($dto);
              $this->repo->save($product);
              if (!empty($dto->companies)) {
                  $product->companies()->attach($dto->companies); // mapa [companyId => ['price' => x]]
              }
              // Disparar evento ProductCreated
              return $product;
          });
      }
  }

- Repository (interfaz simplificada):
  interface ProductRepositoryInterface {
      public function all(): \Illuminate\Support\Collection;
      public function find(int $id): ?Product;
      public function save(Product $product): void;
      public function delete(Product $product): void;
  }


## 21. Guía de extensión y mantenimiento (ampliada)
- Añadir transacciones en store/update/destroy y manejo uniforme de errores JSON (status, message, errors).
- Implementar Policies para autorización por acción.
- Estandarizar el contrato de companies y documentarlo en un README interno del módulo.
- Agregar pruebas Feature para CRUD + asociaciones con compañías (con y sin price).
- Incorporar Observers/Events para logging y auditoría, reduciendo acoplamiento al controlador.
- Añadir seeders/factories para datos de ejemplo (Products, Companies y pivots con price).


## 22. Checklist de integración y despliegue
- Confirmar estructura de DB: existe la tabla company_product y (opcional) la columna price.
- Ejecutar migraciones pendientes y seeders.
- Verificar que el contrato del formulario (companies[*][company_id] y price) coincide con ProductRequest o viceversa.
- Probar flujos CRUD por UI y por AJAX (create/edit/delete y listado JSON).
- Revisar logs de operaciones (crear/eliminar) y que user_id sea correcto.
- Validar N+1 y performance en listados con companies.


## 23. Glosario
- Pivot: Tabla intermedia que mantiene relaciones muchos-a-muchos entre dos entidades (companies y products) con posibles atributos adicionales (price).
- DTO: Objeto de transferencia de datos que encapsula datos de entrada/salida entre capas.
- Factory: Patrón para construir objetos complejos, aislando detalles de creación.
- Named Constructor: Método estático con nombre descriptivo para crear instancias (ej. fromDTO).
- SOLID: Conjunto de principios que promueven diseño mantenible y extensible (Single Responsibility, Open/Closed, Liskov, Interface Segregation, Dependency Inversion).
