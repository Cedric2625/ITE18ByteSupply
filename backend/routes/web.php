<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HardwareComponentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SelectedComponentController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureBuyer;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\AdminDashboardController;

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

// Dashboard
Route::get('/', function () {
	if (auth('admin')->check()) {
		return redirect()->route('admin.dashboard');
	}
	if (auth('buyer')->check()) {
		return redirect()->route('shop');
	}
    return redirect()->route('login');
})->name('dashboard');

// Auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\BuyerRegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\GoogleController;
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
Route::post('/logout', [LoginController::class, 'logout'])->name('auth.logout');
Route::get('/register', [BuyerRegisterController::class, 'show'])->name('auth.register.form');
Route::post('/register', [BuyerRegisterController::class, 'register'])->name('auth.register');

// Password reset (options + OTP)
Route::get('/password/forgot', [PasswordResetController::class, 'showOptions'])->name('password.forgot');
Route::post('/password/old', [PasswordResetController::class, 'changeWithOldPassword'])->name('password.change.old');
Route::post('/password/otp/send', [PasswordResetController::class, 'sendOtp'])->name('password.otp.send');
Route::get('/password/otp/verify', [PasswordResetController::class, 'showVerifyForm'])->name('password.verify.form');
Route::post('/password/otp/verify', [PasswordResetController::class, 'verifyCode'])->name('password.otp.verify');
Route::get('/password/new', [PasswordResetController::class, 'showNewPasswordForm'])->name('password.new.form');
Route::post('/password/new', [PasswordResetController::class, 'setNewPassword'])->name('password.new.set');

// Google OAuth (buyer login + password reset intents)
Route::get('/oauth/google', [GoogleController::class, 'redirectLogin'])->name('oauth.google.login');
Route::get('/oauth/google/callback', [GoogleController::class, 'callbackLogin'])->name('oauth.google.callback');
Route::get('/oauth/google/reset', [GoogleController::class, 'redirectReset'])->name('oauth.google.reset');
Route::get('/oauth/google/reset/callback', [GoogleController::class, 'callbackReset'])->name('oauth.google.reset.callback');
// Alternate callback paths to match Google Console settings
Route::get('/google/redirect', [GoogleController::class, 'callbackLogin'])->name('oauth.google.callback.alt');
Route::get('/google/reset', [GoogleController::class, 'callbackReset'])->name('oauth.google.reset.callback.alt');

// Admin Routes (protected)
Route::middleware(EnsureAdmin::class)->group(function () {
	Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
	Route::resource('admins', AdminController::class);
	Route::get('admins/system', [AdminController::class, 'systemAdmins'])->name('admins.system');
	Route::get('admins/latest', [AdminController::class, 'latest'])->name('admins.latest');
	Route::get('admins/count', [AdminController::class, 'count'])->name('admins.count');

	Route::resource('categories', CategoryController::class);
	Route::get('categories/with-components', [CategoryController::class, 'withComponentsCount'])->name('categories.with-components');
	Route::get('categories/empty', [CategoryController::class, 'empty'])->name('categories.empty');

	Route::resource('suppliers', SupplierController::class);
	Route::get('suppliers/with-components', [SupplierController::class, 'withComponentsCount'])->name('suppliers.with-components');
	Route::get('suppliers/inactive', [SupplierController::class, 'inactive'])->name('suppliers.inactive');

	Route::resource('hardware-components', HardwareComponentController::class);
	Route::get('hardware-components/category/{category}', [HardwareComponentController::class, 'byCategory'])->name('hardware-components.by-category');
	Route::get('hardware-components/supplier/{supplier}', [HardwareComponentController::class, 'bySupplier'])->name('hardware-components.by-supplier');
	Route::get('hardware-components/low-stock', [HardwareComponentController::class, 'lowStock'])->name('hardware-components.low-stock');
	Route::get('hardware-components/ordered/{start}/{end}', [HardwareComponentController::class, 'orderedBetween'])->name('hardware-components.ordered');

	Route::resource('orders', OrderController::class);
	Route::get('orders/buyer/{buyer}', [OrderController::class, 'byBuyer'])->name('orders.by-buyer');
	Route::get('orders/status/{status}', [OrderController::class, 'byStatus'])->name('orders.by-status');
	Route::get('orders/date/{start}/{end}', [OrderController::class, 'byDateRange'])->name('orders.by-date');
	Route::get('orders/statistics', [OrderController::class, 'statistics'])->name('orders.statistics');

	// Buyers management (web)
	Route::resource('buyers', BuyerController::class);

	Route::resource('selected-components', SelectedComponentController::class)->only(['index']);
	Route::get('selected-components/order/{order}', [SelectedComponentController::class, 'byOrder'])->name('selected-components.by-order');
	Route::get('selected-components/component/{component}', [SelectedComponentController::class, 'ordersWithComponent'])->name('selected-components.orders-with-component');
	Route::get('selected-components/most-ordered', [SelectedComponentController::class, 'mostOrdered'])->name('selected-components.most-ordered');
});


// Public shop browse (anyone can view products)
Route::get('/shop', [ShopController::class, 'index'])->name('shop');

// Customer Shop (buyer protected)
Route::middleware(EnsureBuyer::class)->group(function () {
	Route::get('/shop/cart', [ShopController::class, 'cart'])->name('shop.cart');
	Route::post('/shop/add/{hardwareComponent}', [ShopController::class, 'addToCart'])->name('shop.add');
	Route::delete('/shop/remove/{hardwareComponent}', [ShopController::class, 'removeFromCart'])->name('shop.remove');
	Route::get('/shop/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
	Route::post('/shop/place', [ShopController::class, 'placeOrder'])->name('shop.place');
	Route::get('/shop/orders', [ShopController::class, 'myOrders'])->name('shop.orders.index');
	Route::get('/shop/orders/{order}', [ShopController::class, 'myOrderShow'])->name('shop.orders.show');
	Route::get('/shop/settings', [ShopController::class, 'settings'])->name('shop.settings');
});

// Simple test view route
Route::view('/test', 'test')->name('test');

// Example routes removed to avoid duplicate route names