# Pruebas para el Módulo de Productos

Este documento describe cómo implementar y ejecutar diferentes tipos de pruebas para el módulo de productos en nuestra aplicación Laravel.

## Tabla de Contenidos

1. [Pruebas Unitarias](#pruebas-unitarias)
2. [Pruebas de Integración](#pruebas-de-integración)
3. [Pruebas Funcionales](#pruebas-funcionales)
4. [Comandos para Generar Archivos de Prueba](#comandos-para-generar-archivos-de-prueba)
5. [Comandos para Ejecutar Pruebas](#comandos-para-ejecutar-pruebas)

## Pruebas Unitarias

Las pruebas unitarias se centran en probar componentes individuales de la aplicación de forma aislada. Para el módulo de productos, nos enfocaremos en probar el modelo `Product` y sus métodos.

### Comandos para Generar Pruebas Unitarias

```bash
# Generar una prueba unitaria para el modelo Product
php artisan make:test ProductTest --unit
```

### Ejemplo de Prueba Unitaria para el Modelo Product

```php
<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crear_un_producto()
    {
        // Preparación
        $datosProducto = [
            'name' => 'Producto de Prueba',
            'description' => 'Descripción del producto de prueba'
        ];

        // Ejecución
        $producto = new Product();
        $producto->setName($datosProducto['name']);
        $producto->setDescription($datosProducto['description']);
        $producto->save();

        // Verificación
        $this->assertDatabaseHas('products', [
            'name' => 'Producto de Prueba',
            'description' => 'Descripción del producto de prueba'
        ]);
    }

    /** @test */
    public function puede_obtener_nombre_del_producto()
    {
        // Preparación
        $producto = new Product();
        $producto->setName('Producto de Prueba');
        $producto->setDescription('Descripción del producto');
        $producto->save();

        // Ejecución
        $nombreObtenido = $producto->getName();

        // Verificación
        $this->assertEquals('Producto de Prueba', $nombreObtenido);
    }

    /** @test */
    public function puede_obtener_descripcion_del_producto()
    {
        // Preparación
        $producto = new Product();
        $producto->setName('Producto de Prueba');
        $producto->setDescription('Descripción del producto');
        $producto->save();

        // Ejecución
        $descripcionObtenida = $producto->getDescription();

        // Verificación
        $this->assertEquals('Descripción del producto', $descripcionObtenida);
    }

    /** @test */
    public function puede_actualizar_nombre_del_producto()
    {
        // Preparación
        $producto = new Product();
        $producto->setName('Nombre Original');
        $producto->setDescription('Descripción Original');
        $producto->save();

        // Ejecución
        $producto->setName('Nombre Actualizado');
        $producto->save();

        // Verificación
        $this->assertDatabaseHas('products', [
            'id' => $producto->getId(),
            'name' => 'Nombre Actualizado'
        ]);
    }
}
```

## Pruebas de Integración

Las pruebas de integración verifican que diferentes componentes de la aplicación funcionen correctamente juntos. Para el módulo de productos, probaremos la interacción entre el controlador `ProductController` y el modelo `Product`.

### Comandos para Generar Pruebas de Integración

```bash
# Generar una prueba de integración para el controlador ProductController
php artisan make:test ProductControllerTest
```

### Ejemplo de Prueba de Integración para ProductController

```php
<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un usuario para autenticación
        $this->user = User::factory()->create();
    }

    /** @test */
    public function usuario_autenticado_puede_crear_un_producto()
    {
        // Preparación
        $this->actingAs($this->user);
        
        // Crear compañías para asociar al producto
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        
        $datosProducto = [
            'name' => 'Nuevo Producto',
            'description' => 'Descripción del nuevo producto',
            'companies' => [$company1->id, $company2->id]
        ];

        // Ejecución
        $response = $this->postJson('/products/save', $datosProducto);

        // Verificación
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Producto creado con éxito.'
                 ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Nuevo Producto',
            'description' => 'Descripción del nuevo producto'
        ]);

        // Verificar relaciones con compañías
        $producto = Product::where('name', 'Nuevo Producto')->first();
        $this->assertCount(2, $producto->companies);
        $this->assertTrue($producto->companies->contains($company1));
        $this->assertTrue($producto->companies->contains($company2));
    }

    /** @test */
    public function usuario_autenticado_puede_actualizar_un_producto()
    {
        // Preparación
        $this->actingAs($this->user);
        
        // Crear un producto existente
        $producto = new Product();
        $producto->setName('Producto Original');
        $producto->setDescription('Descripción Original');
        $producto->save();
        
        // Crear nuevas compañías para asociar
        $company = Company::factory()->create();
        
        $datosActualizados = [
            'name' => 'Producto Actualizado',
            'description' => 'Descripción Actualizada',
            'companies' => [$company->id]
        ];

        // Ejecución
        $response = $this->putJson("/products/{$producto->id}", $datosActualizados);

        // Verificación
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Producto actualizado con éxito.'
                 ]);

        $this->assertDatabaseHas('products', [
            'id' => $producto->id,
            'name' => 'Producto Actualizado',
            'description' => 'Descripción Actualizada'
        ]);
        
        // Verificar relación con la compañía
        $productoActualizado = Product::find($producto->id);
        $this->assertCount(1, $productoActualizado->companies);
        $this->assertTrue($productoActualizado->companies->contains($company));
    }

    /** @test */
    public function usuario_autenticado_puede_eliminar_un_producto()
    {
        // Preparación
        $this->actingAs($this->user);
        
        // Crear un producto para eliminar
        $producto = new Product();
        $producto->setName('Producto a Eliminar');
        $producto->setDescription('Descripción del producto a eliminar');
        $producto->save();

        // Ejecución
        $response = $this->deleteJson("/products/{$producto->id}/delete");

        // Verificación
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Producto eliminado con éxito.'
                 ]);

        // Verificar que el producto ha sido eliminado (soft delete)
        $this->assertSoftDeleted('products', [
            'id' => $producto->id
        ]);
    }
}
```

## Pruebas Funcionales

Las pruebas funcionales verifican que la aplicación funcione correctamente desde la perspectiva del usuario final. Para el módulo de productos, probaremos las interacciones del usuario con las vistas y formularios.

### Comandos para Generar Pruebas Funcionales

```bash
# Generar una prueba funcional para las vistas de productos
php artisan make:test ProductViewTest
```

### Ejemplo de Prueba Funcional para las Vistas de Productos

```php
<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductViewTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un usuario para autenticación
        $this->user = User::factory()->create();
    }

    /** @test */
    public function usuario_puede_ver_listado_de_productos()
    {
        // Preparación
        $this->actingAs($this->user);
        
        // Crear algunos productos de prueba
        $producto1 = new Product();
        $producto1->setName('Producto 1');
        $producto1->setDescription('Descripción del producto 1');
        $producto1->save();
        
        $producto2 = new Product();
        $producto2->setName('Producto 2');
        $producto2->setDescription('Descripción del producto 2');
        $producto2->save();

        // Ejecución
        $response = $this->get('/products');

        // Verificación
        $response->assertStatus(200);
        $response->assertViewIs('products.index');
        $response->assertViewHas('products');
        $response->assertSee('Producto 1');
        $response->assertSee('Producto 2');
    }

    /** @test */
    public function usuario_puede_ver_formulario_de_creacion()
    {
        // Preparación
        $this->actingAs($this->user);
        
        // Crear algunas compañías para el formulario
        Company::factory()->create(['name' => 'Compañía A']);
        Company::factory()->create(['name' => 'Compañía B']);

        // Ejecución
        $response = $this->get('/products/create');

        // Verificación
        $response->assertStatus(200);
        $response->assertViewIs('products.create');
        $response->assertViewHas('companies');
        $response->assertSee('Compañía A');
        $response->assertSee('Compañía B');
    }

    /** @test */
    public function usuario_puede_ver_formulario_de_edicion()
    {
        // Preparación
        $this->actingAs($this->user);
        
        // Crear un producto para editar
        $producto = new Product();
        $producto->setName('Producto para Editar');
        $producto->setDescription('Descripción del producto para editar');
        $producto->save();
        
        // Crear compañías para el formulario
        Company::factory()->create(['name' => 'Compañía X']);
        Company::factory()->create(['name' => 'Compañía Y']);

        // Ejecución
        $response = $this->get("/products/{$producto->id}/edit");

        // Verificación
        $response->assertStatus(200);
        $response->assertViewIs('products.edit');
        $response->assertViewHas('product');
        $response->assertViewHas('companies');
        $response->assertSee('Producto para Editar');
        $response->assertSee('Compañía X');
        $response->assertSee('Compañía Y');
    }

    /** @test */
    public function usuario_no_autenticado_es_redirigido_al_login()
    {
        // Ejecución
        $response = $this->get('/products');

        // Verificación - debería redirigir al login
        $response->assertRedirect('/login');
    }
}
```

## Comandos para Generar Archivos de Prueba

Laravel proporciona comandos Artisan para generar archivos de prueba:

```bash
# Generar una prueba unitaria
php artisan make:test NombreDeLaPrueba --unit

# Generar una prueba de característica (integración/funcional)
php artisan make:test NombreDeLaPrueba

# Generar una prueba para un modelo específico
php artisan make:test Models/ProductTest --unit

# Generar una prueba para un controlador específico
php artisan make:test Controllers/ProductControllerTest
```

## Comandos para Ejecutar Pruebas

Laravel utiliza PHPUnit para ejecutar pruebas. Aquí están los comandos más comunes:

```bash
# Ejecutar todas las pruebas
php artisan test

# Ejecutar solo pruebas unitarias
php artisan test --testsuite=Unit

# Ejecutar solo pruebas de característica (integración/funcional)
php artisan test --testsuite=Feature

# Ejecutar una prueba específica
php artisan test --filter=ProductTest

# Ejecutar un método de prueba específico
php artisan test --filter=ProductTest::puede_crear_un_producto

# Ejecutar pruebas con información detallada
php artisan test --verbose

# Ejecutar pruebas y generar un informe de cobertura (requiere Xdebug)
XDEBUG_MODE=coverage php artisan test --coverage
```

## Buenas Prácticas para Pruebas

1. **Nombrar claramente las pruebas**: Utiliza nombres descriptivos para tus métodos de prueba que indiquen qué se está probando.
2. **Seguir el patrón AAA (Arrange-Act-Assert)**: Organiza tus pruebas en tres secciones: preparación, ejecución y verificación.
3. **Usar datos de prueba específicos**: Evita usar datos aleatorios cuando sea posible para hacer las pruebas más predecibles.
4. **Aislar las pruebas**: Cada prueba debe ser independiente y no depender del estado dejado por otras pruebas.
5. **Usar factories y seeders**: Utiliza factories para generar datos de prueba de manera eficiente.
6. **Probar casos límite**: No solo pruebes el camino feliz, también prueba casos de error y límites.
7. **Mantener las pruebas rápidas**: Las pruebas lentas desalientan su ejecución frecuente.

## Conclusión

Implementar pruebas exhaustivas para el módulo de productos mejora la calidad del código y reduce la probabilidad de introducir errores al realizar cambios. Las pruebas unitarias, de integración y funcionales proporcionan diferentes niveles de cobertura y confianza en el funcionamiento correcto del sistema.
