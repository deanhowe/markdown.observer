<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PackageMarkdownController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// AI Steering Docs subdomain (production only)
Route::domain('ai.markdown.observer')->group(function () {
    Route::get('/', [App\Http\Controllers\AI\HomeController::class, 'index'])->name('ai.home');
});

// Main domain
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/health', [App\Http\Controllers\HealthController::class, 'index'])->name('health');
Route::get('/ai', [App\Http\Controllers\AI\HomeController::class, 'index'])->name('ai.local'); // Local fallback
Route::get('/pricing', fn() => Inertia::render('Pricing'))->name('pricing');
Route::get('/terms', [App\Http\Controllers\LegalController::class, 'terms'])->name('terms');
Route::get('/privacy', [App\Http\Controllers\LegalController::class, 'privacy'])->name('privacy');
Route::get('/faq', [App\Http\Controllers\LegalController::class, 'faq'])->name('faq');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [PageController::class, 'index'])->name('dashboard');
    
    // Checkout
    Route::post('/checkout/pro-monthly', [App\Http\Controllers\CheckoutController::class, 'proMonthly'])->name('checkout.pro-monthly');
    Route::post('/checkout/pro-yearly', [App\Http\Controllers\CheckoutController::class, 'proYearly'])->name('checkout.pro-yearly');
    Route::post('/checkout/lifetime', [App\Http\Controllers\CheckoutController::class, 'lifetime'])->name('checkout.lifetime');
    Route::get('/billing', [App\Http\Controllers\CheckoutController::class, 'portal'])->name('billing.portal');
    
    // Package upload
    Route::get('/upload', fn() => Inertia::render('UploadPackages'))->name('packages.upload.form');
    Route::post('/upload', [App\Http\Controllers\PackageUploadController::class, 'upload'])->name('packages.upload');
    Route::post('/packages/confirm', [App\Http\Controllers\PackageUploadController::class, 'confirm'])->name('packages.confirm');
    Route::post('/packages/{package}/sync', [App\Http\Controllers\PackageUploadController::class, 'sync'])->name('packages.sync');
    
    // Package docs
    Route::get('/packages/{package}/docs', [App\Http\Controllers\PackageUploadController::class, 'viewDocs'])->name('packages.docs');
    Route::post('/docs/{doc}/update', [App\Http\Controllers\PackageUploadController::class, 'updateDoc'])->name('docs.update');
    
    // Steering docs
    Route::post('/steering/upload', [App\Http\Controllers\SteeringDocController::class, 'upload'])->name('steering.upload');
    
    // Page editor (legacy)
    Route::get('/pages/create', fn() => Inertia::render('PageEditor'))->name('pages.create');
    Route::get('/pages/{slug}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
});

// The carousel route has been moved to routes/api.php

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Package Markdown routes
    Route::get('packages', [PackageMarkdownController::class, 'index'])->name('packages.index');
    Route::get('packages/refresh', [PackageMarkdownController::class, 'refresh'])->name('packages.refresh');
    Route::get('packages/{package}', [PackageMarkdownController::class, 'show'])->name('packages.show');
    Route::get('packages/{package}/markdown/{file-path}', [PackageMarkdownController::class, 'showMarkdownFile'])
        ->name('packages.markdown')
        ->where('file-path', '.*');
    Route::get('packages/{package}/raw/{file-path}', [PackageMarkdownController::class, 'getRawMarkdown'])
        ->name('packages.raw')
        ->where('file-path', '.*');
    Route::get('packages/{package}/html/{file-path}', [PackageMarkdownController::class, 'getHtml'])
        ->name('packages.html')
        ->where('file-path', '.*');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
