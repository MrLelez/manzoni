<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Admin\ImagesController;

// ====================================
// IMAGE API ROUTES
// ====================================

Route::middleware(['auth:sanctum'])->group(function () {
    // Image upload API
    Route::post('/images/upload', [ImageController::class, 'upload'])
        ->name('api.images.upload');
    
    // Admin image management API
    Route::get('/images', [ImagesController::class, 'index'])
        ->name('api.images.index');
    
    Route::put('/images/{image}', [ImagesController::class, 'update'])
        ->name('api.images.update');
    
    Route::delete('/images/{image}', [ImagesController::class, 'destroy'])
        ->name('api.images.destroy');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
