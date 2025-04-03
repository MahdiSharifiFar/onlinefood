<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.index');
})->name('home.index');

Route::get('/about', function () {
    return view('about.about');
})->name('about');

Route::get('/contact', function () {
    return view('contact.index');
})->name('contact.index');

Route::get('/products/{product:slug}', [ProductController::class, 'show'] )->name('products.show');

Route::get('/menu', [ProductController::class, 'menu'] )->name('products.menu');

Route::get('/login', [AuthController::class, 'loginForm'] )->name('auth.loginForm');
Route::get('/logout', [AuthController::class, 'logout'] )->middleware('auth')->name('auth.logout');
Route::post('/login', [AuthController::class, 'login'] )->name('auth.login');
Route::post('/submitOtp', [AuthController::class, 'submitOtp'] )->name('auth.submitOtp');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'] )->name('auth.resendOtp');

Route::post('/contact', [ContactUsController::class , 'store'] )->name('contact.store');

Route::prefix('profile')->middleware('auth')->group(function () {

    Route::get('/', [ProfileController::class, 'index'] )->name('profile.index');
    Route::put('/edit/{user}', [ProfileController::class, 'update'] )->name('profile.update');

    Route::get('/address', [ProfileController::class, 'showAddress'] )->name('profile.showAddress');
    Route::get('/address/create', [ProfileController::class, 'createAddress'] )->name('profile.createAddress');
    Route::post('/address/store', [ProfileController::class, 'storeAddress'] )->name('profile.storeAddress');

    Route::get('/address/edit/{address}', [ProfileController::class, 'editAddress'] )->name('profile.editAddress');
    Route::put('address/update/{address}', [ProfileController::class, 'updateAddress'] )->name('profile.updateAddress');

    Route::get('/favorites', [ProfileController::class, 'favorites'] )->name('profile.favorites');
    Route::get('/favorites/remove/{favorite}', [ProfileController::class, 'removeFavorite'] )->name('profile.favorites.remove');

    Route::get('/orders', [ProfileController::class, 'orders'] )->name('profile.orders');

    Route::get('/transactions', [ProfileController::class, 'transactions'] )->name('profile.transactions');

});

Route::prefix('cart')->middleware('auth')->group(function () {

    Route::get('/', [CartController::class, 'index'] )->name('cart.index');
    Route::get('/increment', [CartController::class, 'increment'] )->name('cart.increment');
    Route::get('/decrement', [CartController::class, 'decrement'] )->name('cart.decrement');
    Route::get('/add', [CartController::class, 'add'] )->name('cart.add');
    Route::get('/remove', [CartController::class, 'remove'] )->name('cart.remove');
    Route::get('/clear', [CartController::class, 'clear'] )->name('cart.clear');
    Route::get('/checkCoupon', [CartController::class, 'checkCoupon'] )->name('cart.checkCoupon');

});

Route::prefix('payment')->middleware('auth')->group(function () {

    Route::post('/send', [PaymentController::class, 'send'] )->name('payment.send');
    Route::get('/verify', [PaymentController::class, 'verify'] )->name('payment.verify');
    Route::get('/status', [PaymentController::class, 'status'] )->name('payment.status');

});

Route::get('/addToFavorites/{product}', [ProfileController::class, 'addToFavorites'] )->name('addToFavorites');
