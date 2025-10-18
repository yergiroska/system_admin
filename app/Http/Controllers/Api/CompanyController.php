<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\Log;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Devuelve una lista de todas las empresas en formato JSON.
     * Incluye URLs para acceder a los detalles de cada empresa.
     *
     * @return JsonResponse Lista de empresas con sus detalles
     */
    final public function listCompanies(): JsonResponse
    {
        $companies = Company::with('products')->get();

        return response()->json([
            'status' => 'success',
            'data' => $companies,
        ]);
    }

    final public function showCompany(int $id): JsonResponse
    {
        // Busca la compañía por ID y carga sus productos
        $company = Company::with('products')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $company,
        ]);
    }

    final public function editCompany(int $id): JsonResponse
    {
        // 1. Cargar compañía con productos asociados
        $company = Company::with('products')->findOrFail($id);

        // 2. Cargar todos los productos
        $allProducts = Product::all();

        // 3. Preparar los productos con información de asociación (¡fuera del response!)
        $products = [];
        foreach ($allProducts as $product) {
            $associatedProduct = $company?->products->firstWhere('id', $product->id);
            $products[] = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'image_url' => $product->image_url,
                'is_associated' => $associatedProduct !== null,
                'price' => $associatedProduct ? $associatedProduct->companyProduct->price : 0.00,
            ];
        }

        $company = $company?->toArray();
        unset($company['products']);
        // 4. Devolver la respuesta limpia
        return response()->json([
            'status' => 'success',
            'data' => [
                'company' => $company,
                'products' => $products,
            ],
        ]);
    }

    /**
     * Almacena una nueva empresa en la base de datos.
     *
     * Este método realiza las siguientes operaciones:
     * 1. Valida los datos de entrada del formulario
     * 2. Crea una nueva empresa
     * 3. Asocia los productos seleccionados
     * 4. Registra la operación en el log del sistema
     * 5. Retorna respuesta JSON con el resultado
     *
     * @param Request $request Contiene los datos del formulario de creación
     * @return JsonResponse Respuesta con el estado de la operación
     */
    public function store(CompanyRequest $request): JsonResponse
    {
        // Validación de campos requeridos
        $request->validated();


        // Creación de nueva instancia de Company y asignación de valores
        $company = new Company();
        $company->name = $request->getName();         // Asigna el nombre de la empresa
        $company->description = $request->getDescription(); // Asigna la descripción
        // Subida de imagen (opcional)
        if ($request->hasFile('image')) {
            // Guardar en storage/app/public/
            $filename = $request->file('image')->hashName();
            $content_image = file_get_contents($request->file('image'));
            Storage::disk('public')->put('images/' . $filename, $content_image);
            $company->image_url = $filename;

        }
        $company->save();   // Guarda la empresa en la base de datos

        // Asocia los productos seleccionados a la empresa (si hay alguno)
        $company->products()->attach($request->getCompaniesProducts() ?? []);

        // Registro de la acción en el sistema de logs
        $user = User::first();

        $userId = auth()->user() ? auth()->user()->id : $user->id;
        $log = new Log();
        $log->action = 'CREAR';                  // Tipo de acción realizada
        $log->objeto = 'Empresas';              // Entidad afectada
        $log->objeto_id = $company->id;        // ID del registro creado
        $log->detail = $company->toJson();      // Detalles de la empresa en formato JSON
        $log->ip = '4444';                     // IP del usuario (valor estático)
        $log->user_id = $userId;    // ID del usuario autenticado
        $log->save();                          // Guarda el registro de log

        // Retorna respuesta JSON con el resultado
        return response()->json([
            'status' => 'success',
            'message' => 'Empresa creada con exito.',
        ]);
    }

    /**
     * Actualiza la información de una empresa existente.
     *
     * @param int $id ID de la empresa a actualizar
     * @param Request $request Datos actualizados de la empresa
     * @return JsonResponse Respuesta con el resultado de la operación
     */
    public function update(int $id, CompanyRequest $request): JsonResponse
    {
        $request->validated();

        $company = Company::find($id);
        $company->name = $request->getName();
        $company->description = $request->getDescription();
        // Si se sube una nueva imagen, opcionalmente elimina la anterior y actualiza
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($company->image_url) {
                Storage::disk('public')->delete('images/'.$company->image_url);
            }

            $filename = $request->file('image')->hashName();
            $content_image = file_get_contents($request->file('image'));
            Storage::disk('public')->put('images/' . $filename, $content_image);
            $company->image_url = $filename;
        }
        $company->save();

        $company->products()->sync($request->getCompaniesProducts() ?? []);

        return response()->json([
            'status' => 'success',
            'message' => 'Empresa actualizada con exito.',
        ]);
    }

    /**
     * Elimina una empresa del sistema.
     * Registra la eliminación en el log del sistema antes de eliminar la empresa.
     *
     * @param int $id ID de la empresa a eliminar
     * @return JsonResponse Respuesta con el resultado de la operación
     */
    public function destroy(int $id): JsonResponse
    {
        $company = Company::find($id);

        if ($company->image_name) {
            Storage::disk('public')->delete('images/'.$company->image_name);
        }

        // Registro de la eliminación en el log
        $user = User::first();
        $userId = auth()->user() ? auth()->user()->id : $user->id;
        $log = new Log();
        $log->action = 'ELIMINAR';
        $log->objeto = 'Empresas';
        $log->objeto_id = $company->id;
        $log->detail = $company->toJson();
        $log->ip = '4444';
        $log->user_id = $userId;
        $log->save();

        // Eliminación del producto
        $company->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Empresa eliminada con exito.',
        ]);
    }

}
