<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ItemsController;
use App\Http\Controllers\API\TotalItemsController;
use App\Http\Controllers\API\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'store']);
Route::get('/', function() {
    return response()->json('test');
});

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::delete('/logout', [AuthController::class, 'logout']);

    // Warehouse
    Route::apiResource('warehouse', WarehouseController::class);
    // Item
    Route::apiResource('item', ItemsController::class);
    // Total Stock
    Route::apiResource('total-stock', TotalItemsController::class);
    Route::post('/total-stock/out', [TotalItemsController::class, 'outStock']);
    Route::post('/total-stock/import', [TotalItemsController::class, 'import']);
});
Route::get('/total-stock/export', [TotalItemsController::class, 'export']);
