<?php
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Ruta de prueba para verificar que funciona
Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando correctamente desde Laravel']);
});

// RUTAS DE AUTENTICACIÓN (sin autenticación requerida)
Route::prefix('authenticate')
    ->name('authenticate.')
    ->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
    });


// Rutas para el controlador de productos
Route::prefix('products')
    ->name('products.')
    ->middleware('api.auth')
    ->group(function () {
        Route::get('/', [ProductController::class, 'listProducts'])->name('lists');  // Listar
        Route::post('/save', [ProductController::class, 'store'])->name('store'); // Guardar nuevo
        Route::put('/{id}/update', [ProductController::class, 'update'])->name('update');     // Actualizar
        Route::delete('/{id}/delete', [ProductController::class, 'destroy'])->name('destroy'); // Eliminar

});

Route::prefix('companies')
    ->name('companies.')
    ->middleware('api.auth')
    ->group(function () {
        Route::get('/', [CompanyController::class, 'listCompanies'])->name('lists');  // Listar
        Route::post('/save', [CompanyController::class, 'store'])->name('store'); // Guardar nuevo
        Route::put('/{id}/update', [CompanyController::class, 'update'])->name('update');     // Actualizar
        Route::delete('/{id}/delete', [CompanyController::class, 'destroy'])->name('destroy'); // Eliminar
});

Route::prefix('customers')
    ->name('customers.')
    ->middleware('api.auth')
    ->group(function () {
        Route::get('/', [CustomerController::class, 'listCustomers'])->name('lists');  // Listar
        Route::post('/save', [CustomerController::class, 'store'])->name('store'); // Guardar nuevo
        Route::put('/{id}/update', [CustomerController::class, 'update'])->name('update');     // Actualizar
        Route::delete('/{id}/delete', [CustomerController::class, 'destroy'])->name('destroy'); // Eliminar
});

