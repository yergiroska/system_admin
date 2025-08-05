<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLoginController;
use Illuminate\Support\Facades\Route;

/***Route de la Vista inicio***/
Route::get('/', function () {
    return view('welcome');
});

/***Route del Dashboard***/
Route::get('/dashboard', function () {
    return view('dashboard');
})
    ->middleware('auth')
    ->name('dashboard');


/***Route de Users****/
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/***Routes Customer***/
Route::prefix('customers')
    ->name('customers.')
    ->middleware('auth')
    ->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');            // Listar
    Route::get('/create', [CustomerController::class, 'create'])->name('create');
    Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');    // Formulario editar

    Route::post('/save', [CustomerController::class, 'store'])->name('store');           // Guardar nuevo para web
    Route::put('/{id}/update', [CustomerController::class, 'update'])->name('update');     // Actualizar
    Route::delete('/{id}/delete', [CustomerController::class, 'destroy'])->name('destroy'); // Eliminar

    Route::get('/view', [CustomerController::class, 'viewCustomers'])->name('view');
    Route::get('/list', [CustomerController::class, 'listCustomers'])->name('lists'); // Listar
});


/***Routes Product***/
Route::prefix('products')
    ->name('products.')
    ->middleware('auth')
    ->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');            // Listar
    Route::get('/create', [ProductController::class, 'create'])->name('create');    // Formulario crear
    Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');    // Formulario editar

    Route::post('/save', [ProductController::class, 'store'])->name('store'); // Guardar nuevo
    Route::put('/{id}/update', [ProductController::class, 'update'])->name('update');     // Actualizar
    Route::delete('/{id}/delete', [ProductController::class, 'destroy'])->name('destroy'); // Eliminar

    Route::get('/view', [ProductController::class, 'viewProducts'])->name('view');  // Listar
    Route::get('/list', [ProductController::class, 'listProducts'])->name('lists');  // Listar
});

/***Routes Note***/
Route::prefix('notes')
    ->name('notes.')
    ->middleware('auth')
    ->group(function () {
    Route::get('/', [NoteController::class, 'index'])->name('index'); // Listar
    Route::get('/create', [NoteController::class, 'create'])->name('create');    // Formulario crear
    Route::get('/{id}/edit', [NoteController::class, 'edit'])->name('edit');    // Formulario editar

    Route::post('/save', [NoteController::class, 'store'])->name('store');  // Guardar nuevo
    Route::put('/{id}/update', [NoteController::class, 'update'])->name('update');     // Actualizar
    Route::delete('/{id}/delete', [NoteController::class, 'destroy'])->name('destroy'); // Eliminar

    Route::get('/view', [NoteController::class, 'viewNotes'])->name('view');  // Listar
    Route::get('/list', [NoteController::class, 'listNotes'])->name('lists');            // Listar
    Route::get('/{id}/show', [NoteController::class, 'show'])->name('show');
});

/***Route de Company***/
Route::prefix('companies')
    ->name('companies.')
    ->middleware('auth')
    ->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/create', [CompanyController::class, 'create'])->name('create');    // Formulario crear
    Route::get('/{id}/edit', [CompanyController::class, 'edit'])->name('edit');

    Route::post('/save', [CompanyController::class, 'store'])->name('store');
    Route::put('/{id}/update', [CompanyController::class, 'update'])->name('update');
    Route::delete('/{id}/delete', [CompanyController::class, 'destroy'])->name('destroy');

    Route::get('/view', [CompanyController::class, 'viewCompanies'])->name('view');  // Listar
    Route::get('/list', [CompanyController::class, 'listCompanies'])->name('lists');           // Listar
    Route::get('/{id}/show', [CompanyController::class, 'show'])->name('show');
});

/***Route de Logs***/
Route::prefix('logs')
    ->name('logs.')
    ->middleware('auth')
    ->group(function () {
    Route::get('/', [LogController::class, 'index'])->name('index');
    Route::get('/{id}/details', [LogController::class, 'details'])->name('details');
});

/***Route de Users***/
Route::prefix('users')
    ->name('users.')
    ->middleware('auth')
    ->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
});

/***Route de UsersLogins***/
Route::prefix('users-logins')
    ->name('user_login.')
    ->middleware('auth')
    ->group(function () {
    Route::get('/{id}/details', [UserLoginController::class, 'details'])->name('details');
});

