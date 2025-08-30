<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Routes para Productos - Sin autenticación por ahora
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);           // GET /api/products
    Route::post('/', [ProductController::class, 'store']);          // POST /api/products
    Route::get('/{id}', [ProductController::class, 'show']);        // GET /api/products/{id}
    Route::put('/{id}', [ProductController::class, 'update']);      // PUT /api/products/{id}
    Route::delete('/{id}', [ProductController::class, 'destroy']);  // DELETE /api/products/{id}
});
