<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartCodeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

// carrito de compras
Route::get('/cart', [CartController::class, 'getCart'])->name('cart.cart')->middleware('auth');
Route::post('/cart', [CartController::class, 'addToCart'])->name('cart.add')->middleware('auth');

Route::group(['prefix' => 'users'], function () {
    Route::get('/profile', [UserController::class, 'show'])->name('users.profile')->middleware('auth');
});

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', [UserController::class, 'index'])->name('users.admin.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.admin.create');
    Route::get('/users/{user}', [UserController::class, 'showUser'])->name('users.admin.show');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/{user}/edit', [UserController::class, 'editUser'])->name('users.admin.edit');
    Route::put('/{user}', [UserController::class, 'updateUser'])->name('users.update');
    Route::get('/{user}/edit-image', [UserController::class, 'editImageUser'])->name('users.admin.image');
    Route::post('/{user}/edit-image', [UserController::class, 'updateImageUser'])->name('users.admin.updateImage');
    Route::post('/users', [UserController::class, 'store'])->name('users.admin.store');
});

Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');

Route::delete('/user', [UserController::class, 'delete'])->middleware('auth');
Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::get('/users/edit-image', [UserController::class, 'editImage'])->name('users.editImage')->middleware('auth');
Route::post('/users/edit-image', [UserController::class, 'updateImage'])->name('users.updateImage')->middleware('auth');

Route::get('/user/password', [UserController::class, 'showChangePasswordForm'])->name('user.password');
Route::post('/user/password', [UserController::class, 'changePassword'])->name('user.password.update');

Route::group(['prefix' => 'addresses'], function () {
    Route::get('/', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('/create', [AddressController::class, 'create'])->name('addresses.create')->middleware(['auth', 'admin']);
    Route::post('/', [AddressController::class, 'store'])->name('addresses.store')->middleware(['auth', 'admin']);
    Route::get('/{address}', [AddressController::class, 'show'])->name('addresses.show');
    Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit')->middleware(['auth', 'admin']);
    Route::put('/{address}', [AddressController::class, 'update'])->name('addresses.update')->middleware(['auth', 'admin']);
    Route::delete('/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy')->middleware(['auth', 'admin']);
});


Auth::routes();
