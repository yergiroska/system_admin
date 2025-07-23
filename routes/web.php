<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/***Routes Customer***/
Route::prefix('customers')->name('customers.')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');            // Listar
    Route::get('/create', [CustomerController::class, 'create'])->name('create');    // Formulario crear
    Route::post('/save', [CustomerController::class, 'store'])->name('store');           // Guardar nuevo

    Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');    // Formulario editar
    Route::put('/{id}', [CustomerController::class, 'update'])->name('update');     // Actualizar

    Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy'); // Eliminar
});


/***Routes Product***/
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');            // Listar
    Route::get('/list', [ProductController::class, 'listProducts'])->name('lists');            // Listar
    Route::get('/create', [ProductController::class, 'create'])->name('create');    // Formulario crear
    Route::post('/save', [ProductController::class, 'store'])->name('store');           // Guardar nuevo

    Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');    // Formulario editar
    Route::put('/{id}', [ProductController::class, 'update'])->name('update');     // Actualizar

    Route::delete('/{id}/delete', [ProductController::class, 'destroy'])->name('destroy'); // Eliminar


    Route::get('/view', [ProductController::class, 'viewProducts'])->name('view.products');            // Listar
});

/***Routes Note***/
Route::prefix('notes')->name('notes.')->group(function () {
    Route::get('/', [NoteController::class, 'index'])->name('index');            // Listar
    Route::get('/list', [NoteController::class, 'listNotes'])->name('lists');            // Listar
    Route::get('/create', [NoteController::class, 'create'])->name('create');    // Formulario crear
    Route::post('/save', [NoteController::class, 'store'])->name('store');           // Guardar nuevo

    Route::get('/{id}/edit', [NoteController::class, 'edit'])->name('edit');    // Formulario editar
    Route::put('/{id}', [NoteController::class, 'update'])->name('update');     // Actualizar

    Route::delete('/{id}', [NoteController::class, 'destroy'])->name('destroy'); // Eliminar


    Route::get('/view', [NoteController::class, 'viewNotes'])->name('view.notes');            // Listar
});
