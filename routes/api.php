<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// all public routs
/** users route */
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

/** stores */
Route::get('/stores', [StoreController::class, 'getStores']);

/** services */
Route::get('/services', [ServiceController::class, 'get']);
Route::get('/services/getByCategory/{id}', [ServiceController::class, 'getByCategory']);
Route::get('/services/getByStore/{id}', [ServiceController::class, 'getByStore']);
Route::get('/services/getById/{id}', [ServiceController::class, 'getById']);

/** categories */
Route::get('/categories', [CategoryController::class, 'get']);
Route::get('/user', function (Request $request) {
    return response()->json($request->user());
});

/** products */
Route::get('/products', [ProductsController::class, 'index']);

/** private routes */
Route::middleware('auth:sanctum')->group(function () {
    /** users route */
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/email/verification-notification', [AuthController::class, 'sendVerificationEmail']);
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

    /** stores */
    Route::get('/stores/{id}', [StoreController::class, 'getStoresById']);
    Route::get('/stores/user/{id}', [StoreController::class, 'getStoresByUserId']);
    Route::post('/stores/add', [StoreController::class, 'addStore']);
    Route::patch('/stores/update', [StoreController::class, 'updateStore']);
    Route::patch('/stores/updateStatus', [StoreController::class, 'updateStoreStatus']);
    Route::delete('/stores/delete/{id}', [StoreController::class, 'deleteStore']);

    /** services */
    Route::post('/services/add', [ServiceController::class, 'add']);
    Route::delete('/services/delete/{id}', [ServiceController::class, 'delete']);
    Route::patch('/services/edit/{id}', [ServiceController::class, 'update']);
    Route::patch('/services/updatePicture/{$id}', [ServiceController::class, 'updatePicture']);
    Route::patch('/services/updateStatus/${$id}', [ServiceController::class, 'updateStatus']);

    /** categories */
    // Route::get('/categories/get', [CategoryController::class, 'get']);
    Route::post('/categories/add', [CategoryController::class, 'add']);
    Route::patch('/categories/update', [CategoryController::class, 'update']);
    Route::delete('/categories/delete/{id}', [CategoryController::class, 'delete']);

    /** products */
    Route::post('/products/add', [ProductsController::class, 'create']);
    Route::patch('/products/edit/{id}', [ProductsController::class, 'update']);
    Route::delete('/products/delete/{id}', [ProductsController::class, 'destroy']);

    /** orders */
    Route::get('/orders', [OrderController::class, 'index']);  // Listar pedidos
    //
    Route::post('/orders/add', [OrderController::class, 'store']);  // Criar pedido
    Route::get('/orders/store/{id}', [OrderController::class, 'getByStore']);  // Buscar pedidos por loja
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);  // Deletar pedido
});
