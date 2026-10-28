<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\LinkPageController;
use App\Http\Controllers\Admin\SourceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductClickController;
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
        Route::post('/links/{link}/products/bulk-action', [LinkPageController::class, 'bulkAction'])->name('links.products.bulk-action');

        // Source Management Routes
        Route::get('/links/{link}/sources', [SourceController::class, 'index'])->name('links.sources.index');
        Route::post('/links/{link}/sources', [SourceController::class, 'store'])->name('links.sources.store');
        Route::put('/links/{link}/sources/{source}', [SourceController::class, 'update'])->name('links.sources.update');
        Route::delete('/links/{link}/sources/{source}', [SourceController::class, 'destroy'])->name('links.sources.destroy');

        // Analytics Routes
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/{link}', [AnalyticsController::class, 'show'])->name('analytics.show');
    });
});

// Product Click Tracking Route (Public — validates product belongs to link, tracks, then redirects)
Route::get('/link/{link}/product/{product}/click', [ProductClickController::class, 'track'])->name('public.product.click');

// System Utility Routes
Route::get('/run-migration', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response("<pre style='background:#1e293b;color:#f8fafc;padding:20px;border-radius:10px;font-family:monospace;'><strong>✓ Migration Output:</strong>\n\n" . e($output ?: 'Migration executed successfully with no pending migrations.') . "</pre>");
    } catch (\Throwable $e) {
        return response("<pre style='background:#ef4444;color:#ffffff;padding:20px;border-radius:10px;font-family:monospace;'><strong>✗ Migration Error:</strong>\n\n" . e($e->getMessage()) . "</pre>", 500);
    }
});

Route::get('/run-seeder', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response("<pre style='background:#1e293b;color:#f8fafc;padding:20px;border-radius:10px;font-family:monospace;'><strong>✓ Seeder Output:</strong>\n\n" . e($output ?: 'Seeder executed successfully.') . "</pre>");
    } catch (\Throwable $e) {
        return response("<pre style='background:#ef4444;color:#ffffff;padding:20px;border-radius:10px;font-family:monospace;'><strong>✗ Seeder Error:</strong>\n\n" . e($e->getMessage()) . "</pre>", 500);
    }
});

Route::get('/clear-all', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response("<pre style='background:#1e293b;color:#f8fafc;padding:20px;border-radius:10px;font-family:monospace;'><strong>✓ Clear All Output:</strong>\n\n" . e($output ?: 'All caches (config, cache, routes, views) cleared successfully.') . "</pre>");
    } catch (\Throwable $e) {
        return response("<pre style='background:#ef4444;color:#ffffff;padding:20px;border-radius:10px;font-family:monospace;'><strong>✗ Clear All Error:</strong>\n\n" . e($e->getMessage()) . "</pre>", 500);
    }
});

Route::get('/run-storage-link', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response("<pre style='background:#1e293b;color:#f8fafc;padding:20px;border-radius:10px;font-family:monospace;'><strong>✓ Storage Link Output:</strong>\n\n" . e($output ?: 'The [public/storage] link has been connected to [storage/app/public].') . "</pre>");
    } catch (\Throwable $e) {
        return response("<pre style='background:#ef4444;color:#ffffff;padding:20px;border-radius:10px;font-family:monospace;'><strong>✗ Storage Link Error:</strong>\n\n" . e($e->getMessage()) . "</pre>", 500);
    }
});

// Public Link Page Route (Must remain at the very end to prevent route collisions)
Route::get('/{slug}', [PublicLinkController::class, 'show'])->name('public.link');
