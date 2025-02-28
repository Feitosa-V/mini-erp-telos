<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('users', UserController::class);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('suppliers', SupplierController::class);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('products', ProductController::class);
});

Route::middleware(['auth:sanctum'])->post('/products/upload', [ProductImportController::class, 'upload']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index']); // Listar pedidos
    Route::post('/orders', [OrderController::class, 'store']); // Criar pedido
    Route::get('/orders/{id}', [OrderController::class, 'show']); // Detalhes do pedido
    Route::put('/orders/{id}', [OrderController::class, 'update']); // Atualizar pedido
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']); // Deletar pedido
});