<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartCodeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

if (env('DEVELOP_MODE') == 'true') {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('v1')->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('shops', ShopController::class);
        Route::apiResource('cartcodes', CartCodeController::class);
        Route::apiResource('books', BookController::class);
        Route::apiResource('orders', OrdersController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('addresses', AddressController::class);

        Route::post('books/{id}/update-image', [BookController::class, 'updateImage']);
        Route::get('users/{id}', [UserController::class, 'showUser']);
        Route::put('users/{id}', [UserController::class, 'updateUser']);
        Route::post('users/{id}/update-image', [UserController::class, 'updateImageUser']);
    });
}
