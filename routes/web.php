<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartCodeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
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

/* carrito de compras */
Route::get('/cart', [CartController::class, 'getCart'])->name('cart.cart')->middleware('auth');
Route::post('/cart', [CartController::class, 'addToCart'])->name('cart.add')->middleware('auth');
Route::delete('/cart', [CartController::class, 'removeFromCart'])->name('cart.remove')->middleware('auth');

/* Rutas de libros y categorías */
Route::get('books/', [BookController::class, 'index'])->name('books.index');
Route::get('books/{book}', [BookController::class, 'show'])->name('books.show');
Route::get('categories/', [CategoryController::class, 'index'])->name('categories.index');
Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

/* Rutas de gestión del usuario autenticado */
Route::get('users/profile', [UserController::class, 'show'])->name('users.profile')->middleware('auth', 'verified');
Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update')->middleware('auth', 'verified');
Route::delete('/user', [UserController::class, 'delete'])->middleware('auth', 'verified');
Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('auth', 'verified');
Route::get('/users/edit-image', [UserController::class, 'editImage'])->name('users.editImage')->middleware('auth', 'verified');
Route::post('/users/edit-image', [UserController::class, 'updateImage'])->name('users.updateImage')->middleware('auth', 'verified');
Route::get('/user/password', [UserController::class, 'showChangePasswordForm'])->name('user.password')->middleware('auth', 'verified');
Route::post('/user/password', [UserController::class, 'changePassword'])->name('user.password.update')->middleware('auth', 'verified');

/* Rutas para las direcciones del usuario autenticado */
Route::get('users/{user}/address/edit', [AddressController::class, 'editUserAddress'])->name('users.address.edit')->middleware('auth', 'verified');
Route::get('users/{user}/address/create', [AddressController::class, 'createUserAddress'])->name('users.address.create')->middleware('auth', 'verified');
Route::post('user/address', [AddressController::class, 'storeUserAddress'])->name('user.address.store')->middleware('auth', 'verified');
Route::put('user/address/{address}', [AddressController::class, 'updateUserAddress'])->name('user.address.update')->middleware('auth', 'verified');
Route::delete('user/address/{address}', [AddressController::class, 'deleteUserAddress'])->name('user.address.delete')->middleware('auth', 'verified');

// no hace falta verificación de cuenta
Route::post('/password/reset/send', [ForgotPasswordController::class, 'sendResetLinkEmailUserLogged'])->name('password.reset.send')->middleware('auth');

/* EMAILS */
Route::get('/user/email/change', [UserController::class, 'changeEmailForm'])->name('user.email.change.form');
Route::post('/user/email/change', [UserController::class, 'requestEmailChange'])->name('user.email.change');
Route::get('/user/email/confirm/{token}', [UserController::class, 'confirmEmailChange'])->name('user.email.confirm');

Route::get('/email/verify', function () {
    if (auth()->user()->hasVerifiedEmail()) {
        return redirect()->route('users.profile');
    }
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('users.profile');
    }
    $request->fulfill();
    flash('Email verificado correctamente')->success()->important();
    return redirect()->route('books.index');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('users.profile');
    }
    $request->user()->sendEmailVerificationNotification();
    return view('auth.verify');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::post('/email/verification-resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('users.profile');
    }
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Email de verificación reenviado');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

///////////////////////
/* Rutas para ADMINS */
///////////////////////
Route::group(['prefix' => 'cartcodes', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/{cartcode}', [CartCodeController::class, 'show'])->name('cartcodes.show');
    Route::get('/', [CartCodeController::class, 'index'])->name('cartcodes.index');
    Route::get('/create', [CartCodeController::class, 'create'])->name('cartcodes.create');
    Route::post('/', [CartCodeController::class, 'store'])->name('cartcodes.store');
    Route::get('/{cartcode}/edit', [CartCodeController::class, 'edit'])->name('cartcodes.edit');
    Route::put('/{cartcode}', [CartCodeController::class, 'update'])->name('cartcodes.update');
    Route::delete('/{cartcode}', [CartCodeController::class, 'destroy'])->name('cartcodes.destroy');
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

Route::group(['prefix' => 'addresses', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/{address}', [AddressController::class, 'show'])->name('addresses.show');
    Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
});

Route::group(['prefix' => 'books', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/', [BookController::class, 'store'])->name('books.store');
    Route::get('/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::get('/{book}/edit-image', [BookController::class, 'editImage'])->name('books.editImage');
    Route::patch('/{book}/edit-image', [BookController::class, 'updateImage'])->name('books.updateImage');
});

Route::group(['prefix' => 'categories', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});
Auth::routes();
