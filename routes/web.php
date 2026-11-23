<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::middleware('guest')->group(function () {
	Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
	Route::get('/auth/google/redirect', [CustomerAuthController::class, 'redirectToGoogle'])->name('auth.google');
	Route::get('/google/callback', [CustomerAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [CustomerAuthController::class, 'logout'])
	->middleware('auth')
	->name('logout');

Route::get('/payments/khalti/return', [CheckoutController::class, 'khaltiReturn'])
	->name('payments.khalti.return');

Route::middleware('signed')
	->get('/admin/orders/{order}/receipt', OrderReceiptController::class)
	->whereNumber('order')
	->name('admin.orders.receipt');

Route::middleware('auth')->group(function () {
	Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
	Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
	Route::patch('/cart/{cart}', [CartController::class, 'update'])->whereNumber('cart')->name('cart.update');
	Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->whereNumber('cart')->name('cart.destroy');

	Route::get('/checkout/{merchant}', [CheckoutController::class, 'show'])->whereNumber('merchant')->name('checkout.show');
	Route::post('/checkout/{merchant}', [CheckoutController::class, 'store'])->whereNumber('merchant')->name('checkout.store');

	Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
	Route::get('/orders/{order}', [OrderController::class, 'show'])->whereNumber('order')->name('orders.show');
});

Route::get('/products', [PageController::class, 'products'])->name('products.index');
Route::get('/products/{slug}', [PageController::class, 'productShow'])->name('products.show');

Route::get('/merchant-register', [PageController::class, 'merchantRegister'])->name('merchant.register');
Route::post('/merchant-register', [PageController::class, 'storeMerchant'])->name('merchant.register.store');

