<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\ProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{

    final public function index(): JsonResponse
    {
        try {
            $products = Product::with('companies')->get();

            return response()->json([
                'success' => true,
                'message' => 'Productos obtenidos correctamente',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    final public function store(ProductRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Crear el producto
            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description']
            ]);

            // Asociar compañías si existen
            if (isset($data['companies']) && is_array($data['companies'])) {
                $companyData = [];
                foreach ($data['companies'] as $company) {
                    if (isset($company['id'])) {
                        $companyData[$company['id']] = [
                            'price' => $company['price'] ?? null
                        ];
                    }
                }
                $product->companies()->attach($companyData);
            }

            // Cargar las relaciones para la respuesta
            $product->load('companies');

            return response()->json([
                'success' => true,
                'message' => 'Producto creado correctamente',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    final public function show(int $id): JsonResponse
    {
        try {
            $product = Product::find($id);

            return response()->json([
                'success' => true,
                'message' => 'Producto obtenido correctamente',
                'data' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    final public function update($id, ProductRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $data = $request->validated();
            $product= Product::find($id);
            $product->name = $data['name'];           // Establece el nombre del producto
            $product->description = $data['description']; // Establece la descripción del producto
            $product->save();

            // Sincronización de relaciones con compañías
            $product->companies()->sync($data['companies'] ?? []);

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado correctamente',
                'data' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    final public function destroy(int $id): JsonResponse
    {
        try {
            $product = Product::find($id);
            // Eliminar las relaciones con compañías
            $product->companies()->detach();

            // Eliminar el producto (soft delete)
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
