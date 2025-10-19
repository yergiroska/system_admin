<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Log;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage; // ← Agregar esta línea
use App\Models\User;
use Illuminate\View\View;

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
     * @return View Vista con la lista de productos
     */
    public function index(): View
    {
        $products = Product::all();
        return view('products.index', [
            'products' => $products,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     *
     * @return View Vista con el formulario de creación y lista de compañías
     */
    public function create(): View
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
        $request->validated();
        // Creación de una nueva instancia del modelo Product y asignación de valores
        $product = new Product();
        $product->name = $request->getName();           // Establece el nombre del producto
        $product->description = $request->getDescription(); // Establece la descripción del producto
        // Subida de imagen (opcional)
        if ($request->hasFile('image')) {
            // Guardar en storage/app/public/
            $filename = $request->file('image')->hashName();
            $content_image = file_get_contents($request->file('image'));
            Storage::disk('public')->put('images/' . $filename, $content_image);
            $product->image_url = $filename;

        }
        $product->save();   // Guarda el nuevo producto en la base de datos

        // Asocia las compañías seleccionadas al producto (si existen)
        // Si no se seleccionaron compañías, se asigna un array vacío
        $product->companiesProducts()->attach($request->getCompaniesProducts() ?? []);

        // Registro de la acción en el sistema de logs

        $user = User::first();

        $userId = auth()->user() ? auth()->user()->id : $user->id;
        $log = new Log();
        $log->action ='CREAR';                  // Tipo de acción realizada
        $log->objeto = 'Productos';   // Tabla/Entidad afectada
        $log->objeto_id = $product->id;         // ID del registro creado
        $log->detail = $product->toJson();   // Detalles del producto en formato JSON
        $log->ip = '2222';  // IP del usuario (valor estático por ahora)
        $log->user_id = $userId;   // ID del usuario que realizó la acción
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
     * @return View Vista para visualización de productos
     */
    public function viewProducts(): View
    {
        return view('products.view_products');
    }

    /**
     * Muestra el formulario para editar un producto existente.
     *
     * @param int $id ID del producto a editar
     * @return View Vista con el formulario de edición y datos del producto
     */
    public function edit(int $id): View
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
     * @param ProductRequest $request Los datos actualizados del producto
     * @return JsonResponse Respuesta JSON con el estado de la operación
     */
    public function update(int $id, ProductRequest $request): JsonResponse
    {
        // Validación de campos requeridos
        $request->validated();
        $product= Product::find($id);
        $product->name = $request->getName();           // Establece el nombre del producto
        $product->description = $request->getDescription(); // Establece la descripción del producto
        // Si se sube una nueva imagen, opcionalmente elimina la anterior y actualiza
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($product->image_url) {
                Storage::disk('public')->delete('images/'.$product->image_url);
            }

            $filename = $request->file('image')->hashName();
            $content_image = file_get_contents($request->file('image'));
            Storage::disk('public')->put('images/' . $filename, $content_image);
            $product->image_url = $filename;
        }
        $product->save();
        // Sincronización de relaciones con compañías
        $product->companiesProducts()->sync($request->getCompaniesProducts() ?? []);

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
    public function destroy(int $id): JsonResponse
    {
        $product = Product::find($id);

        if ($product->image_name) {
            Storage::disk('public')->delete('images/'.$product->image_name);
        }

        // Registro de la eliminación en el log
        $user = User::first();

        $userId = auth()->user() ? auth()->user()->id : $user->id;
        $log = new Log();
        $log->action = 'ELIMINAR';
        $log->objeto = 'Productos';
        $log->objeto_id = $product->id;
        $log->detail = $product->toJson();
        $log->ip = '2222';
        $log->user_id = $userId;
        $log->save();

        // Eliminación del producto
       $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Producto eliminado con éxito.',
        ]);
    }

}
