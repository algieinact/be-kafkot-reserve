<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Force JSON response for API routes
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                // Handle authentication exceptions
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated.',
                        'error' => 'Please login to continue.'
                    ], 401);
                }

                // Handle authorization exceptions
                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized.',
                        'error' => 'You do not have permission to perform this action.'
                    ], 403);
                }

                // Handle HTTP exceptions
                if ($e instanceof HttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage() ?: 'An error occurred.',
                        'error' => $e->getMessage()
                    ], $e->getStatusCode());
                }

                // Handle validation exceptions
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed.',
                        'errors' => $e->errors()
                    ], 422);
                }

                // Handle other exceptions in production
                if (!config('app.debug')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Server error.',
                        'error' => 'An unexpected error occurred.'
                    ], 500);
                }
            }

            // Let Laravel handle non-API exceptions normally
            return null;
        });
    })->create();
