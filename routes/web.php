<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AdminDashboardController;

// ====================================
// IMAGE SERVING ROUTES
// ====================================

Route::get('/img/{cleanName}', [ImageController::class, 'serve'])
    ->name('images.serve')
    ->where('cleanName', '[a-zA-Z0-9\-_]+');


    
// ====================================
// PUBLIC ROUTES
// ====================================

Route::get('/', function () {
    return view('welcome');
})->name('home');

// ====================================
// AUTHENTICATED ROUTES
// ====================================

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        
        if ($user->hasRole('rivenditore')) {
            return redirect()->route('rivenditore.dashboard');
        }
        
        if ($user->hasRole('agente')) {
            return redirect()->route('agente.dashboard');
        }
        
        return view('dashboard');
    })->name('dashboard');
});

// ====================================
// ADMIN ROUTES - USANDO DIRETTAMENTE CLASSE SPATIE
// ====================================

Route::middleware(['auth', \Spatie\Permission\Middleware\RoleMiddleware::class . ':admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // 👥 USERS MANAGEMENT (solo admin)
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminDashboardController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{user}/toggle', [AdminDashboardController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::patch('/users/{user}/level', [AdminDashboardController::class, 'updateUserLevel'])->name('users.level');
    
    // 🛍️ PRODUCTS MANAGEMENT (require: manage-products permission)
    Route::middleware(\Spatie\Permission\Middleware\PermissionMiddleware::class . ':manage-products')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}', App\Livewire\Admin\ProductEditor::class)->name('products.show');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });
    
    // // 🖼️ IMAGES MANAGEMENT (require: manage-images permission)
    // Route::middleware(\Spatie\Permission\Middleware\PermissionMiddleware::class . ':manage-images')->group(function () {
    //     Route::get('/images', [ImageController::class, 'index'])->name('images.index');
    //     Route::post('/images', [ImageController::class, 'store'])->name('images.store');
    //     Route::put('/images/{image}', [ImageController::class, 'update'])->name('images.update');
    //     Route::delete('/images/{image}', [ImageController::class, 'destroy'])->name('images.destroy');
    // });
    
    // 📁 CATEGORIES MANAGEMENT (require: manage-categories permission)
    Route::middleware(\Spatie\Permission\Middleware\PermissionMiddleware::class . ':manage-categories')->group(function () {
        Route::get('/categories', function () {
            return view('admin.coming-soon', [
                'feature' => 'Categories Management',
                'description' => 'Gestione completa delle categorie prodotti con controlli sicurezza.'
            ]);
        })->name('categories.index');
    });
    
    // 🏷️ TAGS MANAGEMENT (require: manage-tags permission)
    Route::middleware(\Spatie\Permission\Middleware\PermissionMiddleware::class . ':manage-tags')->group(function () {
        Route::get('/tags', function () {
            return view('admin.coming-soon', [
                'feature' => 'Tags Management', 
                'description' => 'Gestione completa dei tag con bulk operations e controlli dipendenze.'
            ]);
        })->name('tags.index');
    });
});

// ====================================
// RIVENDITORE ROUTES
// ====================================

Route::middleware(['auth', \Spatie\Permission\Middleware\RoleMiddleware::class . ':rivenditore'])->prefix('rivenditore')->name('rivenditore.')->group(function () {
    
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return view('rivenditore.dashboard', compact('user'));
    })->name('dashboard');
    
    // Catalogo con prezzi personalizzati (require: view-pricing)
    Route::middleware(\Spatie\Permission\Middleware\PermissionMiddleware::class . ':view-pricing')->group(function () {
        Route::get('/catalogo', function () {
            return view('rivenditore.catalogo');
        })->name('catalogo');
    });
    
    // Gestione ordini (require: place-orders)
    Route::middleware(\Spatie\Permission\Middleware\PermissionMiddleware::class . ':place-orders')->group(function () {
        Route::get('/ordini', function () {
            return view('rivenditore.ordini');
        })->name('ordini');
    });
});

// ====================================
// AGENTE ROUTES
// ====================================

Route::middleware(['auth', \Spatie\Permission\Middleware\RoleMiddleware::class . ':agente'])->prefix('agente')->name('agente.')->group(function () {
    
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return view('agente.dashboard', compact('user'));
    })->name('dashboard');
    
    // Catalogo mobile (require: view-catalog)
    Route::middleware(\Spatie\Permission\Middleware\PermissionMiddleware::class . ':view-catalog')->group(function () {
        Route::get('/catalogo', function () {
            return view('agente.catalogo');
        })->name('catalogo');
    });
    
    // Funzionalità offline (require: sync-offline-data)
    Route::middleware(\Spatie\Permission\Middleware\PermissionMiddleware::class . ':sync-offline-data')->group(function () {
        Route::get('/offline', function () {
            return view('agente.offline');
        })->name('offline');
    });
});

// ====================================
// DEBUG ROUTES
// ====================================

if (config('app.debug')) {
    Route::middleware('auth')->group(function () {
        Route::get('/debug-middleware', function () {
            $user = auth()->user();
            return [
                'user' => $user?->email,
                'roles' => $user?->roles->pluck('name'),
                'permissions' => $user?->getAllPermissions()->pluck('name'),
                'spatie_classes' => [
                    'RoleMiddleware' => class_exists('Spatie\Permission\Middleware\RoleMiddleware'),
                    'PermissionMiddleware' => class_exists('Spatie\Permission\Middleware\PermissionMiddleware'),
                ],
                'middleware_test' => 'Routes using direct Spatie classes',
            ];
        })->name('debug.middleware');
    });
}


