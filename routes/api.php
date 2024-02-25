<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CartCodeController;
use App\Http\Controllers\CategoryController;
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
    });

    Route::prefix('v1')->group(function () {
        Route::apiResource('cartcodes', CartCodeController::class);
    });

    Route::prefix('v1')->group(function () {
        Route::apiResource('books', BookController::class);
    });

    Route::post('v1/books/{id}/update-image', [BookController::class, 'updateImage']);
}
