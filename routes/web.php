<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\LinkPageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicLinkController;
use Illuminate\Support\Facades\Route;

// Public Landing Page
Route::get('/', function () {
    return view('landing.index');
})->name('landing');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
});

// Authenticated Admin Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Link Management Routes
        Route::get('/links', [LinkController::class, 'index'])->name('links.index');
        Route::post('/links', [LinkController::class, 'store'])->name('links.store');
        Route::put('/links/{link}', [LinkController::class, 'update'])->name('links.update');
        Route::delete('/links/{link}', [LinkController::class, 'destroy'])->name('links.destroy');
        Route::patch('/links/{link}/toggle-status', [LinkController::class, 'toggleStatus'])->name('links.toggle-status');

        // Link Page Editor Routes ("Atur Halaman")
        Route::get('/links/{link}/edit-page', [LinkPageController::class, 'edit'])->name('links.edit-page');
        Route::put('/links/{link}/edit-page', [LinkPageController::class, 'update'])->name('links.update-page');

        // Category Management Routes
        Route::post('/links/{link}/categories', [LinkPageController::class, 'storeCategory'])->name('links.categories.store');
        Route::put('/links/{link}/categories/{category}', [LinkPageController::class, 'updateCategory'])->name('links.categories.update');
        Route::delete('/links/{link}/categories/{category}', [LinkPageController::class, 'destroyCategory'])->name('links.categories.destroy');

        // Product Management Routes
        Route::post('/links/{link}/products', [LinkPageController::class, 'storeProduct'])->name('links.products.store');
        Route::put('/links/{link}/products/{product}', [LinkPageController::class, 'updateProduct'])->name('links.products.update');
        Route::delete('/links/{link}/products/{product}', [LinkPageController::class, 'destroyProduct'])->name('links.products.destroy');
        Route::patch('/links/{link}/products/{product}/toggle-status', [LinkPageController::class, 'toggleProductStatus'])->name('links.products.toggle-status');
        Route::post('/links/{link}/products/order', [LinkPageController::class, 'updateProductOrder'])->name('links.products.order');
        Route::post('/links/{link}/products/import', [LinkPageController::class, 'importProducts'])->name('links.products.import');
        Route::get('/links/{link}/products/template', [LinkPageController::class, 'downloadProductTemplate'])->name('links.products.template');
    });
});

// Public Link Page Route (Must remain at the very end to prevent route collisions)
Route::get('/{slug}', [PublicLinkController::class, 'show'])->name('public.link');
