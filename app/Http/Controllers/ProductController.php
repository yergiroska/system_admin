<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', [
            'products' => $products
        ]);
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->save();

        $log = new Log();
        $log->action = 'CREAR';
        $log->objeto = 'Productos';
        $log->objeto_id =  $product->id;
        $log->detail = $product->toJson();
        $log->ip = '2222';
        $log->user_id = auth()->user()->id;
        $log->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Producto creado con exito.',
        ]);
    }

    public function viewProducts()
    {
        return view('products.view_products');
    }

    public function listProducts(): JsonResponse
    {
        $products = Product::all();
        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }


    public function edit($id)
    {
        $product = Product::find($id);
        return view('products.edit', [
            'product' => $product
        ]);
    }


    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $product= Product::find($id);
        $product->name = $request->name;
        $product->description = $request->description;
        $product->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Producto actualizado con exito.',
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        $log = new Log();
        $log->action = 'ELIMINAR';
        $log->objeto = 'Productos';
        $log->objeto_id =  $product->id;
        $log->detail = $product->toJson();
        $log->ip = '2222';
        $log->user_id = auth()->user()->id;
        $log->save();

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Producto eliminado con exito.',
        ]);
    }
}
