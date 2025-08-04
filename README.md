<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# System Admin

## Descripción del Proyecto

System Admin es un sistema administrativo desarrollado con Laravel que permite la gestión integral de productos, empresas y clientes. El sistema facilita el mantenimiento de relaciones entre productos y empresas mediante una estructura de muchos a muchos, permitiendo asociar múltiples productos a múltiples empresas. Además, incluye un módulo de clientes para gestionar la información de contacto y seguimiento de los mismos.

## Características Principales

- **Gestión de Productos**: Creación, visualización, edición y eliminación de productos con descripción detallada.
- **Gestión de Empresas**: Administración completa de empresas con información descriptiva.
- **Gestión de Clientes**: Registro y seguimiento de información de clientes.
- **Relaciones Muchos a Muchos**: Asociación flexible entre productos y empresas.
- **Autenticación de Usuarios**: Sistema de login seguro para controlar el acceso.
- **Registro de Actividades**: Seguimiento detallado de todas las acciones realizadas en el sistema mediante logs.
- **Interfaz en Español**: Diseñada para usuarios hispanohablantes.
- **Operaciones Asíncronas**: Implementación de AJAX para mejorar la experiencia del usuario sin recargar páginas.
- **Borrado Lógico**: Implementación de SoftDeletes para mantener historial de datos.

## Módulos del Sistema

### Módulo de Productos
- Gestión completa de productos
- Asociación con empresas
- Validación de datos
- Operaciones CRUD mediante interfaz amigable

### Módulo de Empresas
- Administración de información empresarial
- Visualización de productos asociados
- Validación de datos
- Operaciones CRUD con confirmaciones

### Módulo de Clientes
- Registro de información personal de clientes
- Gestión de documentos de identidad
- Seguimiento de fechas importantes
- Operaciones CRUD con confirmaciones AJAX

### Módulo de Usuarios
- Control de acceso
- Registro de actividades por usuario
- Seguimiento de sesiones

### Módulo de Notas
- Gestión de notas y recordatorios
- Marcado de tareas completadas
- Operaciones CRUD con confirmaciones AJAX
- Registro de actividades en el sistema

### Módulo de Registro (Logs)
- Seguimiento detallado de todas las acciones del sistema
- Registro de creación, modificación y eliminación de registros
- Visualización cronológica de actividades
- Detalles completos de cada operación realizada

## Instalación

1. Clonar el repositorio
   ```
   git clone [url-del-repositorio]
   ```

2. Instalar dependencias
   ```
   composer install
   npm install
   ```

3. Configurar el archivo .env
   ```
   cp .env.example .env
   php artisan key:generate
   ```

4. Configurar la base de datos en el archivo .env

5. Ejecutar migraciones
   ```
   php artisan migrate
   ```

6. Iniciar el servidor
   ```
   php artisan serve
   ```

## Tecnologías Utilizadas

- **Laravel**: Framework PHP para el desarrollo del backend
- **JavaScript/jQuery**: Para funcionalidades AJAX y mejora de experiencia de usuario
- **Blade**: Sistema de plantillas para las vistas
- **Eloquent ORM**: Para manejo de base de datos y relaciones

## Requisitos del Sistema

- PHP 8.0 o superior
- Composer
- MySQL o MariaDB
- Servidor web (Apache/Nginx)

## Uso

### Acceso al Sistema
1. Acceder a la URL del proyecto en el navegador
2. Iniciar sesión con credenciales de usuario
3. Navegar por el menú principal para acceder a los diferentes módulos

### Gestión de Productos
- Para crear un producto: Acceder a Productos > Crear Producto
- Para editar un producto: Seleccionar el producto en la lista y hacer clic en "Editar"
- Para eliminar un producto: Seleccionar el producto y hacer clic en "Eliminar"
- Para asociar empresas: En el formulario de creación/edición, seleccionar las empresas deseadas

### Gestión de Empresas
- Para crear una empresa: Acceder a Empresas > Crear Empresa
- Para ver detalles: Hacer clic en el nombre de la empresa en la lista
- Para editar o eliminar: Usar los botones correspondientes en la lista

### Gestión de Clientes
- Para crear un cliente: Acceder a Clientes > Crear Cliente
- Para gestionar clientes: Usar la interfaz de lista con opciones de edición y eliminación

## Licencia

El framework Laravel es un software de código abierto bajo la [licencia MIT](https://opensource.org/licenses/MIT).
