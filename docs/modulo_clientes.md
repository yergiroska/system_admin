# Documentación del Módulo de Clientes

## Fecha: 06/08/2025

## Índice
1. [Modelo](#modelo)
2. [Controlador](#controlador)
3. [Vistas](#vistas)
4. [Rutas](#rutas)

## Modelo

El módulo de clientes utiliza el modelo `Customer` ubicado en `app\Models\Customer.php`.

### Estructura de la tabla

La tabla `customers` contiene los siguientes campos:
- `id`: Clave primaria
- `first_name`: Nombre del cliente
- `last_name`: Apellido del cliente
- `birth_date`: Fecha de nacimiento
- `identity_document`: Documento de identidad (DNI)
- `deleted_at`: Campo para soft delete
- Timestamps estándar de Laravel (`created_at`, `updated_at`)

### Características del modelo

- Utiliza el trait `SoftDeletes` para permitir la eliminación lógica de registros
- Define los campos que se pueden asignar masivamente mediante `$fillable`
- Oculta el campo `birth_date` en las respuestas JSON mediante `$hidden`
- Agrega el atributo `formatted_birth_date` a las respuestas JSON mediante `$appends`

### Métodos del modelo

#### Getters
- `getId()`: Retorna el ID del cliente
- `getFirstName()`: Retorna el nombre del cliente con la primera letra en mayúscula
- `getLastName()`: Retorna el apellido del cliente con la primera letra en mayúscula
- `getFullName()`: Retorna el nombre completo del cliente (nombre y apellido)
- `getBirthDate()`: Retorna la fecha de nacimiento formateada como 'dd-mm-yyyy'
- `getIdentityDocument()`: Retorna el documento de identidad del cliente
- `getFormattedBirthDateAttribute()`: Accessor para formatear la fecha de nacimiento

#### Setters
- `setFirstName($name)`: Establece el nombre del cliente con la primera letra en mayúscula
- `setLastName($name)`: Establece el apellido del cliente con la primera letra en mayúscula
- `setBirthDate($date)`: Establece la fecha de nacimiento del cliente
- `setIdentityDocument($document)`: Establece el documento de identidad del cliente

## Controlador

El controlador `CustomerController` se encuentra en `app\Http\Controllers\CustomerController.php` y gestiona todas las operaciones relacionadas con los clientes.

### Características del controlador

- Maneja todas las operaciones CRUD (Crear, Leer, Actualizar, Eliminar) para los clientes
- Incluye validación de datos para garantizar la integridad de la información
- Registra logs de las operaciones críticas (crear, eliminar)
- Proporciona respuestas JSON para operaciones AJAX
- Verifica la autenticación de usuarios

### Métodos del controlador

#### Métodos de visualización
- `index()`: Muestra la lista de todos los clientes en la vista principal
- `create()`: Muestra el formulario para crear un nuevo cliente
- `edit($id)`: Muestra el formulario para editar un cliente existente
- `viewCustomers()`: Muestra la vista para visualizar clientes mediante AJAX

#### Métodos de acción
- `store(Request $request)`: Almacena un nuevo cliente en la base de datos
  - Valida los campos requeridos (nombre, apellido, fecha de nacimiento, documento)
  - Crea un nuevo registro de cliente
  - Registra la acción en el log del sistema
  - Devuelve una respuesta JSON con el resultado

- `update($id, Request $request)`: Actualiza la información de un cliente existente
  - Valida los campos requeridos
  - Actualiza el registro del cliente
  - Devuelve una respuesta JSON con el resultado

- `destroy($id)`: Elimina un cliente del sistema
  - Registra la eliminación en el log del sistema
  - Elimina el cliente (soft delete)
  - Devuelve una respuesta JSON con el resultado

#### Métodos de API
- `listCustomers()`: Devuelve una lista de todos los clientes en formato JSON para peticiones AJAX

## Vistas

El módulo de clientes incluye varias vistas ubicadas en `resources\views\customers\`.

### Vista principal (index.blade.php)

Esta vista muestra una tabla con todos los clientes registrados en el sistema.

#### Características:
- Muestra una tabla con la información básica de cada cliente (nombre, apellido, fecha de nacimiento, DNI)
- Proporciona enlaces para crear nuevos clientes y ver la lista de clientes
- Incluye botones para editar y eliminar cada cliente
- Implementa funcionalidad AJAX para eliminar clientes sin recargar la página
- Muestra mensajes de éxito después de realizar operaciones

### Vista de creación (create.blade.php)

Esta vista presenta un formulario para crear nuevos clientes.

#### Características:
- Formulario con campos para nombre, apellido, fecha de nacimiento y DNI
- Validación de datos en el lado del cliente y servidor
- Muestra errores de validación si existen
- Implementa envío del formulario mediante AJAX
- Muestra mensaje de éxito al crear un cliente

### Vista de edición (edit.blade.php)

Esta vista permite modificar la información de un cliente existente.

#### Características:
- Formulario prellenado con los datos actuales del cliente
- Campos para nombre, apellido, fecha de nacimiento y DNI
- Validación de datos en el lado del cliente y servidor
- Implementa envío del formulario mediante AJAX
- Muestra mensaje de éxito al actualizar un cliente

### Vista de visualización (view_customers.blade.php)

Esta vista muestra una tabla de clientes cargada dinámicamente mediante AJAX.

#### Características:
- Estructura de tabla vacía que se rellena mediante una petición AJAX
- Muestra ID, nombre, apellido, fecha de nacimiento y DNI de cada cliente
- Carga los datos de forma asíncrona desde el endpoint `customers.lists`

## Rutas

Las rutas del módulo de clientes están definidas en `routes\web.php` y están agrupadas bajo el prefijo `customers`.

### Configuración de rutas

Todas las rutas del módulo de clientes:
- Tienen el prefijo `customers`
- Están protegidas por el middleware de autenticación (`auth`)
- Tienen nombres que comienzan con `customers.`

### Rutas disponibles

#### Rutas de visualización
- `GET /customers` → `CustomerController@index` → `customers.index`
  - Muestra la página principal con la lista de clientes

- `GET /customers/create` → `CustomerController@create` → `customers.create`
  - Muestra el formulario para crear un nuevo cliente

- `GET /customers/{id}/edit` → `CustomerController@edit` → `customers.edit`
  - Muestra el formulario para editar un cliente existente

- `GET /customers/view` → `CustomerController@viewCustomers` → `customers.view`
  - Muestra la vista para visualizar clientes mediante AJAX

#### Rutas de acción
- `POST /customers/save` → `CustomerController@store` → `customers.store`
  - Procesa el formulario para guardar un nuevo cliente

- `PUT /customers/{id}/update` → `CustomerController@update` → `customers.update`
  - Procesa el formulario para actualizar un cliente existente

- `DELETE /customers/{id}/delete` → `CustomerController@destroy` → `customers.destroy`
  - Elimina un cliente existente

#### Rutas de API
- `GET /customers/list` → `CustomerController@listCustomers` → `customers.lists`
  - Devuelve una lista de todos los clientes en formato JSON
