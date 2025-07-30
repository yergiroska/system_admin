<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
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
            'message' => 'Product updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully.',
        ]);
    }
}
