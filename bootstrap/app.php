<?php
// bootstrap/app.php - VERSIONE CORRETTA con namespace giusto

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Il nostro middleware personalizzato
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        
        // 🎯 SPATIE PERMISSION MIDDLEWARE - NAMESPACE CORRETTO
        $middleware->alias([
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        
        // NOTA: Non registriamo 'spatie.role' per evitare conflitti con il nostro 'role'
        // Il nostro middleware personalizzato è più flessibile per ora
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();