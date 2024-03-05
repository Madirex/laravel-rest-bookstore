<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartCodeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
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

/* carrito de compras */
Route::get('/cart', [CartController::class, 'getCart'])->name('cart.cart')->middleware('auth');
Route::delete('/cart', [CartController::class, 'removeFromCart'])->name('cart.remove')->middleware('auth');
Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout')->middleware('auth');
Route::post('/cart', [CartController::class, 'handleCart'])->name('cart.handle')->middleware('auth');

Route::group(['prefix' => 'cartcodes'], function () {
    Route::get('/', [CartCodeController::class, 'index'])->name('cartcodes.index')->middleware(['auth', 'admin']);
    Route::get('/create', [CartCodeController::class, 'create'])->name('cartcodes.create')->middleware(['auth', 'admin']);
    Route::post('/', [CartCodeController::class, 'store'])->name('cartcodes.store')->middleware(['auth', 'admin']);
    Route::get('/{cartcode}', [CartCodeController::class, 'show'])->name('cartcodes.show')->middleware(['auth', 'admin']);
    Route::get('/{cartcode}/edit', [CartCodeController::class, 'edit'])->name('cartcodes.edit')->middleware(['auth', 'admin']);
    Route::put('/{cartcode}', [CartCodeController::class, 'update'])->name('cartcodes.update')->middleware(['auth', 'admin']);
    Route::delete('/{cartcode}', [CartCodeController::class, 'destroy'])->name('cartcodes.destroy')->middleware(['auth', 'admin']);
});

/* Rutas de libros, categorías y tiendas */

Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
});

Route::group(['prefix' => 'shops'], function () {
    Route::get('/create', [ShopController::class, 'create'])->name('shops.create')->middleware(['auth', 'admin']);
    Route::get('/', [ShopController::class, 'index'])->name('shops.index');
    Route::get('/{shop}', [ShopController::class, 'show'])->name('shops.show');
    Route::post('/', [ShopController::class, 'store'])->name('shops.store')->middleware(['auth', 'admin']);
    Route::get('/{shop}/edit', [ShopController::class, 'edit'])->name('shops.edit')->middleware(['auth', 'admin']);
    Route::put('/{shop}', [ShopController::class, 'update'])->name('shops.update')->middleware(['auth', 'admin']);
    Route::delete('/{shop}', [ShopController::class, 'destroy'])->name('shops.destroy')->middleware(['auth', 'admin']);
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


/* Rutas de gestión del usuario autenticado */
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('users/profile', [UserController::class, 'show'])->name('users.profile');
    Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user', [UserController::class, 'delete']);
    Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::get('/users/edit-image', [UserController::class, 'editImage'])->name('users.editImage');
    Route::post('/users/edit-image', [UserController::class, 'updateImage'])->name('users.updateImage');
    Route::get('/user/password', [UserController::class, 'showChangePasswordForm'])->name('user.password');
    Route::post('/user/password', [UserController::class, 'changePassword'])->name('user.password.update');
    Route::get('users/{user}/address/edit', [AddressController::class, 'editUserAddress'])->name('users.address.edit');
    Route::get('users/{user}/address/create', [AddressController::class, 'createUserAddress'])->name('users.address.create');
    Route::post('user/address', [AddressController::class, 'storeUserAddress'])->name('user.address.store');
    Route::put('user/address/{address}', [AddressController::class, 'updateUserAddress'])->name('user.address.update');
    Route::delete('user/address/{address}', [AddressController::class, 'deleteUserAddress'])->name('user.address.delete');
});

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

/* Rutas de pedidos */
Route::group(['prefix' => 'orders'], function () {
    Route::patch('/{order}/addOrderLine', [OrdersController::class, 'addOrderLine'])->name('orders.addOrderLine')->middleware(['auth']);
    Route::put('/{order}/editOrderLine', [OrdersController::class, 'updateOrderLine'])->name('orders.editOrderLine')->middleware(['auth']);
    Route::delete('/{order}/removeOrderLine/{orderLine}', [OrdersController::class, 'destroyOrderLine'])->name('orders.destroyOrderLine')->middleware(['auth']);
});

Route::get('/orders/{id}/email_invoice', [OrdersController::class, 'generateInvoiceToEmail'])->name('orders.email_invoice')->middleware(['auth']);
Route::get('/orders/{id}/invoice', [OrdersController::class, 'generateInvoice'])->name('orders.invoice')->middleware(['auth']);

///////////////////////
/* Rutas para ADMINS */
///////////////////////
Route::group(['prefix' => 'orders', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('/create', [OrdersController::class, 'create'])->name('orders.create');
    Route::post('/', [OrdersController::class, 'store'])->name('orders.store');
    Route::get('/{order}', [OrdersController::class, 'show'])->name('orders.show');
    Route::get('/{order}/edit', [OrdersController::class, 'edit'])->name('orders.edit');
    Route::put('/{order}', [OrdersController::class, 'update'])->name('orders.update');
    Route::delete('/{order}', [OrdersController::class, 'destroy'])->name('orders.destroy');
    Route::patch('/{order}/addCartCode', [OrdersController::class, 'addCartCode'])->name('orders.addCartCode');
    Route::patch('/{order}/removeCartCode', [OrdersController::class, 'removeCartCode'])->name('orders.removeCartCode');
});

Route::group(['prefix' => 'cartcodes', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', [CartCodeController::class, 'index'])->name('cartcodes.index');
    Route::get('/create', [CartCodeController::class, 'create'])->name('cartcodes.create');
    Route::post('/', [CartCodeController::class, 'store'])->name('cartcodes.store');
    Route::get('/{cartcode}', [CartCodeController::class, 'show'])->name('cartcodes.show');
    Route::get('/{cartcode}/edit', [CartCodeController::class, 'edit'])->name('cartcodes.edit');
    Route::put('/{cartcode}', [CartCodeController::class, 'update'])->name('cartcodes.update');
    Route::delete('/{cartcode}', [CartCodeController::class, 'destroy'])->name('cartcodes.destroy');
});

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', [UserController::class, 'index'])->name('users.admin.index');
    Route::get('/create', [UserController::class, 'create'])->name('users.admin.create');
    Route::get('/{user}', [UserController::class, 'showUser'])->name('users.admin.show');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/{user}/edit', [UserController::class, 'editUser'])->name('users.admin.edit');
    Route::put('/{user}', [UserController::class, 'updateUser'])->name('users.update');
    Route::get('/{user}/edit-image', [UserController::class, 'editImageUser'])->name('users.admin.image');
    Route::post('/{user}/edit-image', [UserController::class, 'updateImageUser'])->name('users.admin.updateImage');
    Route::post('/users', [UserController::class, 'store'])->name('users.admin.store');
});

Route::group(['prefix' => 'users'], function () {
    Route::get('/profile', [UserController::class, 'show'])->name('users.profile')->middleware('auth');
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

Route::group(['prefix' => 'categories', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

Auth::routes();
