<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    final public function getCompaniesWithProducts(): JsonResponse
    {
        $companies = Company::with('products')
            ->has('products') // 👈 Solo empresas que tienen productos
            ->get();

        $data = $companies->map(function ($company) {
            return [
                'id' => $company->id,
                'name' => $company->name,
                'products' => $company->products->map(function ($product) {
                    return [
                        'id' => $product->companyProduct->id,
                        'name' => $product->name,
                        'price' => $product->companyProduct->price
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
