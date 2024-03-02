<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CartCodeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('books.index');
});

Route::group(['prefix' => 'books'], function () {
    Route::get('/', [BookController::class, 'index'])->name('books.index');
    Route::get('/create', [BookController::class, 'create'])->name('books.create')->middleware(['auth', 'admin']);
    Route::post('/', [BookController::class, 'store'])->name('books.store')->middleware(['auth', 'admin']);
    Route::get('/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/{book}/edit', [BookController::class, 'edit'])->name('books.edit')->middleware(['auth', 'admin']);
    Route::put('/{book}', [BookController::class, 'update'])->name('books.update')->middleware(['auth', 'admin']);
    Route::delete('/{book}', [BookController::class, 'destroy'])->name('books.destroy')->middleware(['auth', 'admin']);
    Route::get('/{book}/edit-image', [BookController::class, 'editImage'])->name('books.editImage')->middleware(['auth', 'admin']);
    Route::patch('/{book}/edit-image', [BookController::class, 'updateImage'])->name('books.updateImage')->middleware(['auth', 'admin']);
});






Route::group(['prefix' => 'cartcodes'], function () {
    Route::get('/', [CartCodeController::class, 'index'])->name('cartcodes.index')->middleware(['auth', 'admin']);
    Route::get('/create', [CartCodeController::class, 'create'])->name('cartcodes.create')->middleware(['auth', 'admin']);
    Route::post('/', [CartCodeController::class, 'store'])->name('cartcodes.store')->middleware(['auth', 'admin']);
    Route::get('/{cartcode}', [CartCodeController::class, 'show'])->name('cartcodes.show');
    Route::get('/{cartcode}/edit', [CartCodeController::class, 'edit'])->name('cartcodes.edit')->middleware(['auth', 'admin']);
    Route::put('/{cartcode}', [CartCodeController::class, 'update'])->name('cartcodes.update')->middleware(['auth', 'admin']);
    Route::delete('/{cartcode}', [CartCodeController::class, 'destroy'])->name('cartcodes.destroy')->middleware(['auth', 'admin']);
});

Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/create', [CategoryController::class, 'create'])->name('categories.create')->middleware(['auth', 'admin']);
    Route::post('/', [CategoryController::class, 'store'])->name('categories.store')->middleware(['auth', 'admin']);
    Route::get('/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit')->middleware(['auth', 'admin']);
    Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware(['auth', 'admin']);
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware(['auth', 'admin']);
});

Route::group(['prefix' => 'shops'], function () {
    Route::get('/', [ShopController::class, 'index'])->name('shops.index');
    Route::get('/create', [ShopController::class, 'create'])->name('shops.create')->middleware(['auth', 'admin']);
    Route::post('/', [ShopController::class, 'store'])->name('shops.store')->middleware(['auth', 'admin']);
    Route::get('/{shop}', [ShopController::class, 'show'])->name('shops.show');
    Route::get('/{shop}/edit', [ShopController::class, 'edit'])->name('shops.edit')->middleware(['auth', 'admin']);
    Route::put('/{shop}', [ShopController::class, 'update'])->name('shops.update')->middleware(['auth', 'admin']);
    Route::delete('/{shop}', [ShopController::class, 'destroy'])->name('shops.destroy')->middleware(['auth', 'admin']);

});

Route::group(['prefix' => 'users'], function () {
    Route::get('/profile', [UserController::class, 'show'])->name('users.profile')->middleware('auth');
});

Route::delete('/user', [UserController::class, 'delete'])->middleware('auth');

Auth::routes();
