<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/***Route de la Vista inicio***/
Route::get('/', function () {
    return view('welcome');
});

/***Route del Dashboard***/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

/***Routes Customer***/
Route::prefix('customers')->name('customers.')->group(function () {
    //Rutas para WEB (Formularios)
    Route::get('/create', [CustomerController::class, 'create'])->name('create');
    Route::get('/list', [CustomerController::class, 'listCustomers'])->name('lists'); // Formulario crear
    Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');    // Formulario editar

    Route::get('/', [CustomerController::class, 'index'])->name('index');            // Listar
    Route::post('/save', [CustomerController::class, 'store'])->name('store');           // Guardar nuevo para web
    //Route::post('/', [CustomerController::class, 'store'])->name('store');           // Guardar nuevo para Api
    Route::put('/{id}', [CustomerController::class, 'update'])->name('update');     // Actualizar
    Route::delete('/{id}/delete', [CustomerController::class, 'destroy'])->name('destroy'); // Eliminar
    Route::get('/view', [CustomerController::class, 'viewCustomers'])->name('view.customers');
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
    Route::get('/', [NoteController::class, 'index'])->name('index'); // Listar
    Route::get('/list', [NoteController::class, 'listNotes'])->name('lists');            // Listar
    Route::get('/create', [NoteController::class, 'create'])->name('create');    // Formulario crear
    Route::post('/save', [NoteController::class, 'store'])->name('store');  // Guardar nuevo
    Route::get('/view', [NoteController::class, 'viewNotes'])->name('view.notes');  // Listar
    Route::get('/{id}/edit', [NoteController::class, 'edit'])->name('edit');    // Formulario editar
    Route::put('/{id}', [NoteController::class, 'update'])->name('update');     // Actualizar
    Route::delete('/{id}/delete', [NoteController::class, 'destroy'])->name('destroy'); // Eliminar
    Route::get('/{id}', [NoteController::class, 'show'])->name('show');
});


/***Route de Users****/
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/***Route de Logs***/
Route::prefix('logs')->name('logs.')->group(function () {
    Route::get('/', [LogController::class, 'index'])->name('index');
    Route::get('/{id}/details', [LogController::class, 'details'])->name('details');
});

/***Route de Company***/
Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/create', [CompanyController::class, 'create'])->name('create');    // Formulario crear
    Route::post('/save', [CompanyController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [CompanyController::class, 'edit'])->name('edit');
    Route::put('/{id}', [CompanyController::class, 'update'])->name('update');
    Route::delete('/{id}/delete', [CompanyController::class, 'destroy'])->name('destroy');
});


