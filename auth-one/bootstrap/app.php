<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // *** এইখানে তোমার RoleMiddleware টি Alias হিসেবে রেজিস্টার করা হলো ***
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class, // 'role' Alias টি এখন Routes এ ব্যবহার করা যাবে
        ]);
        
        // তুমি যদি web বা api এর জন্য global middleware যোগ করতে চাও, এইখানে যোগ করতে পারো:
        // $middleware->web(\App\Http\Middleware\SomeWebMiddleware::class);
        // $middleware->api(\App\Http\Middleware\SomeApiMiddleware::class);
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();