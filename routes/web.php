<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Admin\ImagesController;
use App\Http\Controllers\Admin\ProductController;

// ====================================
// IMAGE SERVING ROUTES (DEVONO ESSERE TRA LE PRIME!)
// ====================================

// Serve image by clean name: /img/cestino-roma-blue.jpg
Route::get('/img/{cleanName}', [ImageController::class, 'serve'])
    ->name('images.serve')
    ->where('cleanName', '[a-zA-Z0-9\-_]+');

// Home page pubblica
Route::get('/', function () {
    return view('welcome');
});

// Dashboard principale con redirect automatico basato su ruolo
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // Redirect automatico basato su ruolo
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        
        if ($user->hasRole('rivenditore')) {
            return redirect()->route('rivenditore.dashboard');
        }
        
        if ($user->hasRole('agente')) {
            return redirect()->route('agente.dashboard');
        }
        
        // Default dashboard per utenti senza ruoli specifici
        return view('dashboard');
    })->name('dashboard');
});

// Admin routes - Controllo totale sistema
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [App\Http\Controllers\Admin\AdminDashboardController::class, 'users'])->name('users');
    Route::get('/users/create', [App\Http\Controllers\Admin\AdminDashboardController::class, 'createUser'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\Admin\AdminDashboardController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{user}/toggle', [App\Http\Controllers\Admin\AdminDashboardController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::patch('/users/{user}/level', [App\Http\Controllers\Admin\AdminDashboardController::class, 'updateUserLevel'])->name('users.level');
    // Images management
    Route::get('/images', [ImagesController::class, 'index'])
        ->name('admin.images.index');
    Route::post('/images', [ImagesController::class, 'store'])
        ->name('admin.images.store');
    Route::put('/images/{image}', [ImagesController::class, 'update'])
        ->name('admin.images.update');
    Route::delete('/images/{image}', [ImagesController::class, 'destroy'])
        ->name('admin.images.destroy');
    Route::resource('products', ProductController::class);
});

// Rivenditore routes - Ecommerce con livelli 1-5
Route::middleware(['auth', 'role:rivenditore'])->prefix('rivenditore')->name('rivenditore.')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return view('rivenditore.dashboard', compact('user'));
    })->name('dashboard');
    
    Route::get('/catalogo', function () {
        return view('rivenditore.catalogo');
    })->name('catalogo');
    
    Route::get('/ordini', function () {
        return view('rivenditore.ordini');
    })->name('ordini');
});

// Agente routes - Catalogo mobile
Route::middleware(['auth', 'role:agente'])->prefix('agente')->name('agente.')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return view('agente.dashboard', compact('user'));
    })->name('dashboard');
    
    Route::get('/catalogo', function () {
        return view('agente.catalogo');
    })->name('catalogo');
    
    Route::get('/offline', function () {
        return view('agente.offline');
    })->name('offline');
});

// Debug routes (rimuovi in produzione)
Route::middleware('auth')->group(function () {
    Route::get('/debug-auth', function () {
        $user = auth()->user();
        return [
            'logged_in' => auth()->check(),
            'user_email' => $user ? $user->email : 'None',
            'user_roles' => $user ? $user->roles->pluck('name') : 'None',
            'is_admin' => $user ? $user->hasRole('admin') : false,
            'available_routes' => [
                'dashboard' => route('dashboard'),
                'admin_dashboard' => $user && $user->hasRole('admin') ? route('admin.dashboard') : 'Not accessible',
                'rivenditore_dashboard' => $user && $user->hasRole('rivenditore') ? route('rivenditore.dashboard') : 'Not accessible',
                'agente_dashboard' => $user && $user->hasRole('agente') ? route('agente.dashboard') : 'Not accessible',
            ]
        ];
    });
    
    Route::get('/test-redirect', function () {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return "ADMIN DETECTED - Redirect to: " . route('admin.dashboard');
        }
        
        if ($user->hasRole('rivenditore')) {
            return "RIVENDITORE DETECTED - Redirect to: " . route('rivenditore.dashboard');
        }
        
        if ($user->hasRole('agente')) {
            return "AGENTE DETECTED - Redirect to: " . route('agente.dashboard');
        }
        
        return "User role: " . ($user->roles->first()->name ?? 'No role');
    });
});