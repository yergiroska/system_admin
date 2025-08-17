<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    /**
     * Prueba unitaria para verificar la creación correcta de un producto
     *
     * Este método prueba el proceso completo de creación de un producto siguiendo el patrón AAA (Arrange-Act-Assert)
     */
    final public function puede_crear_un_producto(): void
    {
        // Preparación (Arrange)
        // Se definen los datos de prueba para crear el nuevo producto
        $datosProducto = [
            'name' => 'Producto de Prueba',          // Nombre del producto a crear
            'description' => 'Descripción del producto de prueba', // Descripción del producto
        ];

        // Ejecución (Act)
        // Se crea una nueva instancia del modelo Product y se establecen sus propiedades
        $producto = new Product();                   // Instancia del modelo
        $producto->setName($datosProducto['name']);  // Establece el nombre
        $producto->setDescription($datosProducto['description']); // Establece la descripción
        $producto->save();                          // Guarda el producto en la base de datos

        // Verificación (Assert)
        // Comprueba que el producto se haya guardado correctamente en la base de datos
        $this->assertDatabaseHas('products', [       // Verifica la existencia en la tabla 'products'
            'name' => 'Producto de Prueba',          // Comprueba el nombre
            'description' => 'Descripción del producto de prueba', // Comprueba la descripción
        ]);
    }

    #[Test]
    /**
     * Prueba unitaria para verificar la obtención del nombre de un producto
     *
     * Este método prueba que se pueda obtener correctamente el nombre de un producto previamente creado,
     * siguiendo el patrón AAA (Arrange-Act-Assert):
     *
     * - Arrange (Preparación): Crea y guarda un nuevo producto con datos de prueba
     * - Act (Ejecución): Obtiene el nombre del producto usando el método getName()
     * - Assert (Verificación): Comprueba que el nombre obtenido coincida con el nombre asignado
     */
    final public function puede_obtener_nombre_del_producto(): void
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

    #[Test]
    final public function puede_actualizar_nombre_del_producto(): void
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
