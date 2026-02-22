<?php

use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API routes (no authentication required)
// Client-side logging
Route::post('/logs', [LogController::class, 'store'])->name('api.logs.store');

// Composer packages API routes
Route::prefix('composer-packages')->name('api.composer-packages.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ComposerPackageController::class, 'index'])->name('index');
    Route::get('/{package-name}', [\App\Http\Controllers\ComposerPackageController::class, 'show'])->name('show');
});

// Package carousel route
Route::get('/packages/carousel', [\App\Http\Controllers\PackageMarkdownController::class, 'getPackagesForCarousel'])->name('api.packages.carousel');

// Protected API routes (authentication required)
Route::middleware(['auth:sanctum'])->group(function () {
    // Page API routes
    Route::prefix('pages')->name('api.pages.')->group(function () {
        Route::get('/', [PageController::class, 'index'])->name('index');
        Route::post('/', [PageController::class, 'store'])->name('store');
        Route::get('/disks/list', [PageController::class, 'getDisks'])->name('disks');
        Route::get('/{file-name}', [PageController::class, 'show'])->name('show');
        Route::put('/{file-name}', [PageController::class, 'update'])->name('update');
        Route::delete('/{file-name}', [PageController::class, 'destroy'])->name('destroy');
    });

    // Markdown conversion API routes
    Route::prefix('markdown')->name('api.markdown.')->group(function () {
        Route::post('/to-html', [PageController::class, 'convertToHtml'])->name('to-html');
        Route::post('/to-markdown', [PageController::class, 'convertToMarkdown'])->name('to-markdown');
        Route::post('/export-for-ai', function (\Illuminate\Http\Request $request, \App\Actions\ExportMarkdownForAiAction $action) {
            $request->validate(['markdown' => 'required|string']);
            return response()->json(['markdown' => $action->execute($request->input('markdown'))]);
        })->name('export-for-ai');
    });
});
