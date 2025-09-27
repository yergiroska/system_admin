<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Company;
use App\Models\Log;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
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

    public function showProduct($id): JsonResponse
    {
        // Busca al producto por ID y carga sus companies
        $product = Product::with('companies')->find($id);
        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }

    final public function editProduct(int $id): JsonResponse
    {
        // 1. Cargar el producto con companies asociados
        $product = Product::with('companies')->findOrFail($id);

        // 2. Cargar todas las companies
        $allCompanies = Company::all();

        // 3. Prepara las compañías con información de asociación (¡fuera del response!)
        $companies = [];
        foreach ($allCompanies as $company) {
            $associatedCompany = $product?->companies->firstWhere('id', $company->id);
            $companies[] = [
                'id' => $company->id,
                'name' => $company->name,
                'description' => $company->description,
                'image_url' => $company->image_url,
                'is_associated' => $associatedCompany !== null,
                'price' => $associatedCompany ? $associatedCompany->companyProduct->price : 0.00,
            ];
        }

        $product = $product?->toArray();
        unset($product['companies']);
        // 4. Devolver la respuesta limpia
        return response()->json([
            'status' => 'success',
            'data' => [
                'product' => $product,
                'companies' => $companies,
            ],
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
        $product->name =$data['name'];           // Establece el nombre del producto
        $product->description = $data['description']; // Establece la descripción del producto
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
        $product->companies()->attach($data['companies'] ?? []);

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
     * Actualiza la información de un producto existente.
     *
     * @param int $id ID del producto a actualizar
     * @param ProductRequest $request Los datos actualizados del producto
     * @return JsonResponse Respuesta JSON con el estado de la operación
     */
    public function update($id, ProductRequest $request)
    {
        // Validación de campos requeridos
        $data = $request->validated();
        $product= Product::find($id);
        $product->name = $data['name'];           // Establece el nombre del producto
        $product->description = $data['description']; // Establece la descripción del producto
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
        $product->companies()->sync($data['companies'] ?? []);

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
