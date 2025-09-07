<?php

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
        Route::get('/view', [ProductController::class, 'viewProducts'])->name('view');  // Listar
});