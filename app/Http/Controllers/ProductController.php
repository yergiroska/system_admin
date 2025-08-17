<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Log;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Requests\ProductRequest;


/**
 * Controlador para la gestión de productos en el sistema.
 *
 * Este controlador maneja todas las operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 * relacionadas con los productos, incluyendo:
 * - Listado de productos
 * - Creación de nuevos productos
 * - Actualización de información de productos
 * - Eliminación de productos
 * - Gestión de relaciones con compañías
 *
 * También se encarga de:
 * - Validación de datos de entrada
 * - Registro de logs para operaciones críticas (crear, eliminar)
 * - Manejo de respuestas JSON para operaciones AJAX
 * - Verificación de autenticación de usuarios
 */
class ProductController extends Controller
{

    /**
     * Muestra la lista de todos los productos.
     *
     * @return \Illuminate\View\View Vista con la lista de productos
     */
    public function index()
    {
        $products = Product::all();
        return view('products.index', [
            'products' => $products,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     *
     * @return \Illuminate\View\View Vista con el formulario de creación y lista de compañías
     */
    public function create()
    {
        $companies = Company::all();
        return view('products.create', [
            'companies' => $companies,
        ]);
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     *
     * Este método se encarga de:
     * 1. Validar los datos de entrada del formulario
     * 2. Crear un nuevo registro de producto
     * 3. Asociar las compañías relacionadas
     * 4. Registrar la acción en el log del sistema
     * 5. Devolver una respuesta JSON con el resultado
     *
     * @param ProductRequest $request Contiene los datos validados del formulario de creación del producto
     * @return JsonResponse Respuesta JSON con el estado de la operación
     */
    public function store(ProductRequest $request): JsonResponse
    {
        // Los datos ya están validados por ProductRequest
        $data = $request->validated();

        // Creación de una nueva instancia del modelo Product y asignación de valores
        $product = new Product();
        $product->setName($data['name']);           // Establece el nombre del producto
        $product->setDescription($data['description']); // Establece la descripción del producto
        $product->save();   // Guarda el nuevo producto en la base de datos

        // Asocia las compañías seleccionadas al producto (si existen)
        // Si no se seleccionaron compañías, se asigna un array vacío
        $product->companies()->attach($data['companies'] ?? []);

        // Registro de la acción en el sistema de logs
        $log = new Log();
        $log->setAction('CREAR');                  // Tipo de acción realizada
        $log->setObjeto('Productos');   // Tabla/Entidad afectada
        $log->setObjetoId($product->id);         // ID del registro creado
        $log->setDetail($product->toJson());   // Detalles del producto en formato JSON
        $log->setIp('2222');  // IP del usuario (valor estático por ahora)
        $log->setUserId(auth()->user()->id);   // ID del usuario que realizó la acción
        $log->save();                            // Guarda el registro de log

        // Devuelve respuesta JSON con el resultado exitoso de la operación
        return response()->json([
            'status' => 'success',                    // Estado de la operación
            'message' => 'Producto creado con éxito.', // Mensaje informativo
        ]);
    }

    /**
     * Muestra la vista para visualizar productos.
     *
     * @return \Illuminate\View\View Vista para visualización de productos
     */
    public function viewProducts()
    {
        return view('products.view_products');
    }

    /**
     * Devuelve una lista de todos los productos en formato JSON.
     *
     * Utilizado para peticiones AJAX en la interfaz de usuario.
     *
     * @return JsonResponse Lista de productos en formato JSON
     */
    public function listProducts(): JsonResponse
    {
        $products = Product::all();
        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }


    /**
     * Muestra el formulario para editar un producto existente.
     *
     * @param int $id ID del producto a editar
     * @return \Illuminate\View\View Vista con el formulario de edición y datos del producto
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $companies = Company::all();
        return view('products.edit', [
            'product' => $product,
            'companies' => $companies,
        ]);
    }


    /**
     * Actualiza la información de un producto existente.
     *
     * @param int $id ID del producto a actualizar
     * @param Request $request Los datos actualizados del producto
     * @return JsonResponse Respuesta JSON con el estado de la operación
     */
    public function update($id, Request $request)
    {
        // Validación de campos requeridos
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $product= Product::find($id);
        $product->setName($request->name);
        $product->setDescription($request->description);
        $product->save();

        // Sincronización de relaciones con compañías
        $product->companies()->sync($request->companies ?? []);

        return response()->json([
            'status' => 'success',
            'message' => 'Producto actualizado con éxito.',
        ]);
    }

    /**
     * Elimina un producto del sistema.
     *
     * Registra la eliminación en el log del sistema antes de eliminar el producto.
     *
     * @param int $id ID del producto a eliminar
     * @return JsonResponse Respuesta JSON con el estado de la operación
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        // Registro de la eliminación en el log
        $log = new Log();
        $log->setAction('ELIMINAR');
        $log->setObjeto('Productos');
        $log->setObjetoId($product->id);
        $log->setDetail($product->toJson());
        $log->setIp('2222');
        $log->setUserId(auth()->user()->id);
        $log->save();

        // Eliminación del producto
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Producto eliminado con éxito.',
        ]);
    }

}
