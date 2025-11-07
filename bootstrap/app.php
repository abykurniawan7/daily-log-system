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
    ->withMiddleware(function (Middleware $middleware) {
        // Tambahkan alias middleware kita di sini
        $middleware->alias([
            'supervisi' => \App\Http\Middleware\SupervisiMiddleware::class,
            'admin' => \App\Http\Middleware\CheckAdmin::class,
            'karyawan' => \App\Http\Middleware\KaryawanMiddleware::class,
            'perizinan' => \App\Http\Middleware\PerizinanMiddleware::class,
            'guest.check' => \App\Http\Middleware\CheckGuest::class,
            'session.timeout' => \App\Http\Middleware\SessionTimeout::class,
            'optimize.response' => \App\Http\Middleware\OptimizeResponse::class,
        ]);

         $middleware->web(append: [
            \App\Http\Middleware\RedirectAdminToDashboard::class,
            \App\Http\Middleware\CheckGuest::class,
            \App\Http\Middleware\SessionTimeout::class,
            \App\Http\Middleware\OptimizeResponse::class,
            \App\Http\Middleware\QueryLogger::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\HandleTokenMismatch::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
        //     // Logout user jika session expired
        //     if (auth()->check()) {
        //         auth()->logout();
        //         $request->session()->invalidate();
        //         $request->session()->regenerateToken();
        //     }
            
        //     // Redirect ke homepage
        //     return redirect('/')
        //         ->with('info', 'Sesi Anda telah berakhir. Silakan login kembali jika ingin melanjutkan.');
        // });
    })->create();