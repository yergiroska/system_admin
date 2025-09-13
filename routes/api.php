<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Ruta de prueba para verificar que funciona
Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando correctamente']);
});


// Rutas para el controlador de productos
Route::prefix('products')
    ->name('products.')
    ->group(function () {
        Route::get('/', [ProductController::class, 'listProducts'])->name('lists');  // Listar
        Route::post('/save', [ProductController::class, 'store'])->name('store'); // Guardar nuevo
        Route::put('/{id}/update', [ProductController::class, 'update'])->name('update');     // Actualizar
        Route::delete('/{id}/delete', [ProductController::class, 'destroy'])->name('destroy'); // Eliminar

});

Route::prefix('companies')
    ->name('companies.')
    ->group(function () {
        Route::get('/', [CompanyController::class, 'listCompanies'])->name('lists');  // Listar
        Route::post('/save', [CompanyController::class, 'store'])->name('store'); // Guardar nuevo
        Route::put('/{id}/update', [CompanyController::class, 'update'])->name('update');     // Actualizar
        Route::delete('/{id}/delete', [CompanyController::class, 'destroy'])->name('destroy'); // Eliminar
         // Listar
    });
