<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        
        // Trust all proxies for shared hosting
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle 419 CSRF Token Mismatch - redirect back with message
        $exceptions->renderable(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session expired. Please refresh and try again.'], 419);
            }
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Your session has expired. Please try again.');
        });

        // Handle 404 Not Found - redirect to dashboard with message
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Resource not found.'], 404);
            }
            return redirect()->route('dashboard')
                ->with('error', 'The page you requested was not found.');
        });

        // Handle 405 Method Not Allowed
        $exceptions->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Method not allowed.'], 405);
            }
            return redirect()->back()
                ->with('error', 'Invalid request method. Please try again.');
        });

        // Handle other HTTP exceptions (500, 503, etc.)
        $exceptions->renderable(function (HttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Something went wrong. Please try again.'], $e->getStatusCode());
            }
            
            $message = match($e->getStatusCode()) {
                403 => 'You do not have permission to access this resource.',
                500 => 'Something went wrong. Please try again later.',
                503 => 'Service temporarily unavailable. Please try again in a few minutes.',
                default => 'An error occurred. Please try again.',
            };
            
            return redirect()->back()
                ->with('error', $message);
        });

        // Handle all other exceptions
        $exceptions->renderable(function (Throwable $e, Request $request) {
            // Only handle in production, let dev see real errors
            if (config('app.debug')) {
                return null; // Let Laravel handle it normally in debug mode
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Something went wrong.'], 500);
            }
            
            // Log the actual error
            \Log::error('Unhandled exception: ' . $e->getMessage(), [
                'exception' => $e,
                'url' => $request->fullUrl(),
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->back()
                ->with('error', 'Something went wrong. Please try again.');
        });
    })->create();
